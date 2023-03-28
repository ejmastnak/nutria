-- Display amount and PDV for nutrients in a given meal

\timing on
\set meal_id 3
\set rdi_profile_id 1

-- Print meal name
select name from meals where id = :'meal_id';

select
  nutrients.name as nutrient,
  round(sum(meal_ingredients.mass_in_grams * ingredient_nutrients.amount_per_100g / 100), 2) as amount,
  units.name as unit,
  round(sum(meal_ingredients.mass_in_grams * ingredient_nutrients.amount_per_100g / nullif(rdi_profile_nutrients.rdi, 0)), 1)  || '%' as pdv
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
inner join units
  on units.id
  = nutrients.unit_id
inner join rdi_profile_nutrients
  on rdi_profile_nutrients.rdi_profile_id
  = :'rdi_profile_id'
  and rdi_profile_nutrients.nutrient_id
  = ingredient_nutrients.nutrient_id
group by nutrients.id, units.name;
