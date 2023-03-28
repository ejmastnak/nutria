-- Display amount and PDV for nutrients in a given meal group

\timing on
\set meal_group_id 2
\set rdi_profile_id 1

-- Print meal_group name
select name from meal_groups where id = :'meal_group_id';

select
  nutrients.name as nutrient,
  round(sum(meal_ingredients.mass_in_grams * ingredient_nutrients.amount_per_100g / 100), 2) as amount,
  units.name as unit,
  round(sum(meal_ingredients.mass_in_grams * ingredient_nutrients.amount_per_100g / nullif(rdi_profile_nutrients.rdi, 0)), 1)  || '%' as pdv
from ingredient_nutrients
inner join meal_group_meals
  on meal_group_meals.meal_group_id
  = :'meal_group_id'
inner join meal_ingredients
  on ingredient_nutrients.ingredient_id
  = meal_ingredients.ingredient_id
  and meal_ingredients.meal_id
  = meal_group_meals.meal_id
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
