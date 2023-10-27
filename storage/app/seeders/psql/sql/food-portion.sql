-------------------------------------------------------------------------------
-- Populates and preprocesses the food_portion table
-- Prerequisite: temporary mass/volume and ingredients tables are populated
-------------------------------------------------------------------------------
-- Standardize names of volume units (e.g. set both "tbsp" and "tablespoon" to
-- "tbsp")
update sr.food_portion set modifier = tmp_volume_units.cname from tmp_volume_units where tmp_volume_units.name like sr.food_portion.modifier;

-- Normalize apparently erroneous entries with amount zero
update sr.food_portion set amount = 1 where amount like '0';

-- Create new column for the ingredient_id used in Nutria DB
alter table sr.food_portion add column ingredient_id bigint;

-- Populates ingredient_id with the id of ingredient for which
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
    fp.id::int,
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
-- ----------------------------------------------------------------------------
