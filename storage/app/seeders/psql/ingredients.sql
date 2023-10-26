--------------------------------------------------------------------------------
-- Create and populate SR schema
--------------------------------------------------------------------------------
drop schema if exists sr cascade;
create schema sr;

create table sr.food(
  fdc_id text,
  data_type text,
  description text,
  food_category_id text,
  publication_date text
);

create table sr.food_nutrient(
  id text,
  fdc_id text,
  nutrient_id text,
  amount_per_100g text,
  data_points text,
  derivation_id text,
  min text,
  max text,
  median text,
  footnote text,
  min_year_acquired text
);

create table sr.food_portion(
  id int,
  fdc_id text,
  seq_num text,
  amount text,
  measure_unit_id text,
  portion_description text,
  modifier text,
  gram_weight text,
  data_points text,
  footnote text,
  min_year_acquired text
);

-- Import SR tables from CSV
\COPY sr.food FROM ./csv/food.csv WITH DELIMITER ',' HEADER CSV;
\COPY sr.food_nutrient FROM ./csv/food_nutrient.csv WITH DELIMITER ',' HEADER CSV;
\COPY sr.food_portion FROM ./csv/food_portion.csv WITH DELIMITER ',' HEADER CSV;
--------------------------------------------------------------------------------


--------------------------------------------------------------------------------
-- Create temporary unit-related tables
--------------------------------------------------------------------------------
drop table if exists tmp_mass_units;
create table tmp_mass_units(name text);

-- Can't use a temporary table because of foreign key constraint
drop table if exists tmp_volume_units;
create table tmp_volume_units(
  name text,
  cname text,
  ml double precision,
  unit_id integer references units(id)
);

-- Import unit-related tables from CSV
\COPY tmp_volume_units (name, cname, ml) FROM ./csv/volume_units.csv WITH DELIMITER ',' HEADER CSV;
\COPY tmp_mass_units FROM ./csv/mass_units.csv WITH DELIMITER ',' HEADER CSV;
--------------------------------------------------------------------------------


--------------------------------------------------------------------------------
-- Populate ingredients table
--------------------------------------------------------------------------------
delete from units where ingredient_id is not null;
alter table units drop constraint units_ingredient_id_foreign;
truncate table ingredients restart identity cascade;
alter table units add constraint units_ingredient_id_foreign foreign key (ingredient_id) references ingredients(id);

-- Populates ingredients table.
-- Prerequisite: `ingredient_categories` is populated.
-- Ingredients{{{
insert into ingredients (
  fdc_id,
  name,
  ingredient_category_id,
  ingredient_nutrient_amount,
  ingredient_nutrient_amount_unit_id,
  created_at,
  updated_at
)
select
  sr.food.fdc_id::int,
  sr.food.description,
  sr.food.food_category_id::int,
  100,
  (select id from units where name like 'g' limit 1),
  (now() at time zone 'utc'),
  (now() at time zone 'utc')
from sr.food
inner join ingredient_categories
  on sr.food.food_category_id::int
  = ingredient_categories.id;
/*}}}*/
--------------------------------------------------------------------------------


--------------------------------------------------------------------------------
-- Populate ingredient_nutrients table
--------------------------------------------------------------------------------
truncate table ingredient_nutrients restart identity;
-- Populates the ingredient_nutrients table.
-- Prerequisite: `nutrients` and `ingredients` are populated.

insert into ingredient_nutrients (
  ingredient_id,
  nutrient_id,
  amount,
  amount_per_100g
)
select
  ingredients.id,  -- use local ingredient id and not FDC id
  nutrients.id,
  sr.food_nutrient.amount_per_100g::decimal(10, 3),
  sr.food_nutrient.amount_per_100g::decimal(10, 3)
from sr.food_nutrient
inner join ingredients  -- to access local ingredient id
  on ingredients.fdc_id
  = sr.food_nutrient.fdc_id::int
inner join nutrients
  on nutrients.id
  = sr.food_nutrient.nutrient_id::int;
--------------------------------------------------------------------------------


--------------------------------------------------------------------------------
-- Process the food_portion table
--------------------------------------------------------------------------------
-- Populate unit_id column in tmp_volume_units
update tmp_volume_units set unit_id = (select id from units where units.name like tmp_volume_units.cname limit 1);

-- Normalize apparently erroneous entries with amount zero
update sr.food_portion set amount = 1 where amount like '0';

-- Create new column for the ingredient_id used in Nutria DB
alter table sr.food_portion add column ingredient_id bigint;

-- Populate ingredient_id with the id of ingredient for which
-- ingredients.fdc_id = food_portion.fdc_id
update sr.food_portion set
  ingredient_id = ingredients.id
from ingredients
where sr.food_portion.fdc_id::int = ingredients.fdc_id;

-- Delete any FDC ingredients not in Nutria's ingredient database
delete from sr.food_portion where ingredient_id is null;

-- Dedup food_portion table: for records with a given `fdc_id` and `modifier`
-- pair, i.e. the same ingredient and the same unit, keep only the record with
-- the lowest amount.
drop view if exists food_portion_deduped;
create view food_portion_deduped as
  select
    fp.fdc_id::int,
    fp.ingredient_id,
    fp.amount::double precision,
    fp.modifier,
    fp.seq_num::int,
    fp.gram_weight::double precision
  from (
    select fdc_id, min(amount) as min_amount, modifier
    from sr.food_portion group by fdc_id, modifier
  ) tmp
  inner join sr.food_portion fp
  on tmp.fdc_id = fp.fdc_id
  and tmp.modifier = fp.modifier
  and tmp.min_amount = fp.amount
order by fp.fdc_id;

-- Populate units table with custom, ingredient-specific units.
-- ----------------------------------------------------------------------------
-- Remove any existing ingredient-specific units
delete from units where ingredient_id is not null;

-- Reset unit id counter
select setval(pg_get_serial_sequence('units', 'id'), coalesce(max(id), 1))
from units;

-- Populate units table with custom units (ignoring mass and volume units)
insert into units (name, g, ml, seq_num, ingredient_id, meal_id, custom_unit_amount, custom_mass_amount, custom_mass_unit_id, custom_grams)
  select
    food_portion_deduped.modifier,
    null,
    null,
    (select count(*) from units)+food_portion_deduped.seq_num - 1,
    food_portion_deduped.ingredient_id,
    null,
    food_portion_deduped.amount,
    food_portion_deduped.gram_weight,
    (select id from units where name like 'g' limit 1),
    (food_portion_deduped.gram_weight/food_portion_deduped.amount)
  from food_portion_deduped
  where food_portion_deduped.modifier not like all (select name from tmp_mass_units)
  and food_portion_deduped.modifier not like all (select name from tmp_volume_units);
-- ----------------------------------------------------------------------------

-- Update ingredients table with densities
-- ----------------------------------------------------------------------------
-- Select all `food_portion` records where `modifier` is a volume unit, and
-- compute corresponding density in grams per milliliter
drop view if exists food_portion_volume;
create view food_portion_volume as
  select
    fpd.fdc_id,
    fpd.ingredient_id,
    fpd.amount,
    fpd.gram_weight,
    vu.name as unit,
    vu.unit_id as volume_unit_id,
    vu.ml,
    (fpd.gram_weight / (fpd.amount * vu.ml)) as density_g_ml
  from food_portion_deduped fpd
  inner join tmp_volume_units vu
  on fpd.modifier = vu.name
order by fpd.fdc_id;

-- For volume records with a given fdc_id, keep only the record with largest
-- gram_weight, which will have the smallest relative error in density
drop view if exists food_portion_volume_deduped;
create view food_portion_volume_deduped as
  select
    fpv.fdc_id,
    fpv.ingredient_id,
    fpv.amount,
    fpv.gram_weight,
    fpv.unit,
    fpv.volume_unit_id,
    fpv.ml,
    fpv.density_g_ml
  from (
    select fdc_id, max(gram_weight) as max_gram_weight
    from food_portion_volume group by fdc_id
  ) tmp
  inner join food_portion_volume fpv
  on tmp.fdc_id = fpv.fdc_id
  and tmp.max_gram_weight = fpv.gram_weight
order by fpv.fdc_id;

-- Update ingredients table with densities
update ingredients set
  density_mass_unit_id = (select id from units where name like 'g' limit 1),
  density_mass_amount = fpvd.gram_weight,
  density_volume_unit_id = fpvd.volume_unit_id,
  density_volume_amount = fpvd.amount,
  density_g_ml = fpvd.density_g_ml
from (select
  ingredient_id, gram_weight, volume_unit_id, amount, density_g_ml from food_portion_volume_deduped
) as fpvd (ingredient_id, gram_weight, volume_unit_id, amount, density_g_ml)
where fpvd.ingredient_id = ingredients.id;

-- Clean up
drop table if exists tmp_mass_units cascade;
drop table if exists tmp_volume_units cascade;
-- ----------------------------------------------------------------------------
