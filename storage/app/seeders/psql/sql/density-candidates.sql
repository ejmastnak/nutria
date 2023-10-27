-------------------------------------------------------------------------------
-- Prepares a human-readable list of all food_portion records for which the
-- underlying ingredient has *only* volume units, with the condition that for
-- ingredients with multiple volume units, only the unit with the largest
-- gram_weight is kept, since this unit will have the smallest relative error
-- in density.
-- Prerequisite: food_portion table is populated and preprocessed
-------------------------------------------------------------------------------
-- Select all food_portion records where the underlying ingredient has *only*
-- volume unit records, and compute corresponding density in grams per
-- milliliter
drop view if exists food_portion_volume;
create view food_portion_volume as
select fpd.id,
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
-- This (yucky, inefficient) clause filters out food_portion entries where
-- the ingredient includes records with non-volume units.
and fpd.fdc_id::int not in (
  select fdc_id::int
  from sr.food_portion
  where modifier not like all (
      select cname from tmp_volume_units group by cname
  ) group by fdc_id
)
order by fpd.fdc_id;

-- For volume records with a given fdc_id, keep only the record with largest
-- gram_weight, which will have the smallest relative error in density
drop view if exists food_portion_volume_deduped;
create view food_portion_volume_deduped as
  select
    fpv.id,
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

-- Query candidate ingredients for which to compute a density
select fpvd.id,
  fpvd.fdc_id,
  ingredient_categories.name,
  ingredients.name
from food_portion_volume_deduped fpvd
inner join ingredients
on ingredients.fdc_id = fpvd.fdc_id
inner join ingredient_categories
on ingredient_categories.id = ingredients.ingredient_category_id
order by ingredient_categories.name;
-- ----------------------------------------------------------------------------
