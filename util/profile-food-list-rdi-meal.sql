-- Display amount and PDV for nutrients in a given food list
-- Possibly useful: https://stackoverflow.com/questions/63386879/sql-append-data-from-one-column-to-another-in-same-table

\timing on
\set food_list_id 3
\set rdi_profile_id 1

-- Print food list name
select id, name from food_lists where id = :'food_list_id';

-- Pring all meals in food list
select
  meals.name,
  food_list_meals.mass_in_grams
from food_list_meals 
inner join meals
  on food_list_meals.meal_id
  = meals.id
where food_list_meals.food_list_id = :'food_list_id';

-- -- Print mass of all ingredients in all food list meals
-- select
--   ingredients.name as name,
--   round(meal_ingredients.mass_in_grams * (food_list_meals.mass_in_grams / meals.mass_in_grams), 2) as mass
-- from ingredients
-- inner join food_list_meals
--   on food_list_meals.food_list_id
--   = :'food_list_id'
-- inner join meals
--   on meals.id
--   = food_list_meals.meal_id
-- inner join meal_ingredients
--   on food_list_meals.meal_id
--   = meal_ingredients.meal_id
--   and meal_ingredients.ingredient_id
--   = ingredients.id;

-- Compute nutrient profile
select
  nutrients.name as nutrient,
  -- Note scaling by food_list_meals.mass_in_grams / meals.mass_in_grams
  round(sum(ingredient_nutrients.amount_per_100g * (meal_ingredients.mass_in_grams / 100) * (food_list_meals.mass_in_grams / meals.mass_in_grams)), 2) as amount,
  units.name as unit,
  round(sum(ingredient_nutrients.amount_per_100g * (meal_ingredients.mass_in_grams / nullif(rdi_profile_nutrients.rdi, 0)) * (food_list_meals.mass_in_grams / meals.mass_in_grams)), 1)  || '%' as pdv
from ingredient_nutrients
inner join food_list_meals
  on food_list_meals.food_list_id
  = :'food_list_id'
inner join meals
  on food_list_meals.meal_id
  = meals.id
inner join meal_ingredients
  on ingredient_nutrients.ingredient_id
  = meal_ingredients.ingredient_id
  and meal_ingredients.meal_id
  = food_list_meals.meal_id
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
