\timing on
\set ingredient_id 6435
\set meal_id 1
\set rdi_profile_id 1

select nutrients.name, sum(result.amount) as amount, units.name, sum(result.pdv) as pdv from (
  select
    nutrients.id as nutrient_id,
    round(ingredient_nutrients.amount_per_100g, 2) as amount,
    round((ingredient_nutrients.amount_per_100g / nullif(rdi_profile_nutrients.rdi, 0)) * 100, 1) as pdv
  from ingredient_nutrients
  inner join nutrients
    on nutrients.id
    = ingredient_nutrients.nutrient_id  
  inner join rdi_profile_nutrients
    on rdi_profile_nutrients.rdi_profile_id
    = :'rdi_profile_id'
    and rdi_profile_nutrients.nutrient_id
    = ingredient_nutrients.nutrient_id
  where ingredient_nutrients.ingredient_id=:'ingredient_id'
  union all
  select
    nutrients.id as nutrient_id,
    round(sum(ingredient_nutrients.amount_per_100g * meal_ingredients.mass_in_grams / 100), 2) as amount,
    round(sum(ingredient_nutrients.amount_per_100g * meal_ingredients.mass_in_grams / nullif(rdi_profile_nutrients.rdi, 0)), 1) as pdv
  from ingredient_nutrients
  inner join meals
    on meals.id
    = :'meal_id'
  inner join meal_ingredients
    on ingredient_nutrients.ingredient_id
    = meal_ingredients.ingredient_id
    and meal_ingredients.meal_id
    = meals.id
  inner join nutrients
    on nutrients.id
    = ingredient_nutrients.nutrient_id  
  inner join rdi_profile_nutrients
    on rdi_profile_nutrients.rdi_profile_id
    = :'rdi_profile_id'
    and rdi_profile_nutrients.nutrient_id
    = ingredient_nutrients.nutrient_id
  group by nutrients.id
) result
inner join nutrients
  on nutrients.id
  = result.nutrient_id
inner join units
  on units.id
  = nutrients.unit_id
group by nutrients.id, units.name;

select nutrients.name, sum(result.amount) as amount, units.name, sum(result.pdv) as pdv from (
  select
    nutrient_id,
    amount,
    pdv
  from foo
  union all
  select
    nutrient_id,
    amount,
    pdv
  from bar
) result
inner join nutrients
  on nutrients.id
  = result.nutrient_id
inner join units
  on units.id
  = nutrients.unit_id
group by nutrients.id, units.name;
