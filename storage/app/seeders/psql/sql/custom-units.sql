-------------------------------------------------------------------------------
-- Populates units table with custom units (ignoring mass and volume units)
-- Prerequisites: food_portion table is populated and preprocessed, and
-- temporary mass unit and density whitelist tables are populated
-------------------------------------------------------------------------------
insert into units (name, g, ml, seq_num, ingredient_id, meal_id, custom_unit_amount, custom_mass_amount, custom_mass_unit_id, custom_grams)
  select food_portion_deduped.modifier,
    null,
    null,
    (select count(*) from units) + food_portion_deduped.seq_num - 1,
    food_portion_deduped.ingredient_id,
    null,
    food_portion_deduped.amount,
    food_portion_deduped.gram_weight,
    (select id from units where name like 'g' limit 1),
    (food_portion_deduped.gram_weight/food_portion_deduped.amount)
  from food_portion_deduped
  where food_portion_deduped.modifier not like all (select name from tmp_mass_units)
  and food_portion_deduped.fdc_id not in (select fdc_id from tmp_density_whitelist);
-- ----------------------------------------------------------------------------
