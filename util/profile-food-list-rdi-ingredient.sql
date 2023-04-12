-- Display amount and PDV for nutrients in a given food list
-- Possibly useful: https://stackoverflow.com/questions/63386879/sql-append-data-from-one-column-to-another-in-same-table

\timing on
\set food_list_id 1
\set rdi_profile_id 1

-- Print food list name
select id, name from food_lists where id = :'food_list_id';

-- Print all ingredients in food list
select
  ingredients.name,
  food_list_ingredients.mass_in_grams
from food_list_ingredients 
inner join ingredients
  on food_list_ingredients.ingredient_id
  = ingredients.id
where food_list_ingredients.food_list_id = :'food_list_id';

select
  nutrients.name as nutrient,
  round(sum(ingredient_nutrients.amount_per_100g * food_list_ingredients.mass_in_grams / 100), 2) as amount,
  units.name as unit,
  round(sum(ingredient_nutrients.amount_per_100g * food_list_ingredients.mass_in_grams / nullif(rdi_profile_nutrients.rdi, 0)), 1) as pdv
from ingredient_nutrients
inner join food_list_ingredients
  on ingredient_nutrients.ingredient_id
  = food_list_ingredients.ingredient_id
  and food_list_ingredients.food_list_id
  = :'food_list_id'
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
