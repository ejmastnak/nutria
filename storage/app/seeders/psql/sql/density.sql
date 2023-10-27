-------------------------------------------------------------------------------
-- Updates ingredients table with ingredient density
-- Prerequisites: food_portion table is populated and preprocessed, and
-- temporary volume unit and density whitelist tables are populated
-------------------------------------------------------------------------------
update ingredients set
  density_mass_unit_id = (select id from units where name like 'g' limit 1),
  density_mass_amount = food_portion_deduped.gram_weight,
  density_volume_unit_id = tmp_volume_units.unit_id,
  density_volume_amount = food_portion_deduped.amount,
  density_g_ml = food_portion_deduped.gram_weight / (food_portion_deduped.amount * tmp_volume_units.ml)
from food_portion_deduped
inner join tmp_volume_units
on tmp_volume_units.cname like food_portion_deduped.modifier
where ingredients.id = food_portion_deduped.ingredient_id
and food_portion_deduped.id in (select food_portion_id from tmp_density_whitelist);
-- ----------------------------------------------------------------------------
