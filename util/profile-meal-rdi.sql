-- Display amount and PDV for nutrients in a given meal

\timing on
\set meal_id 1
\set intake_guideline_id 1

-- Print meal name
select id, name from meals where id = :'meal_id';

-- Pring all ingredients in meals
select
  ingredients.name,
  meal_ingredients.mass_in_grams
from meal_ingredients 
inner join ingredients
  on meal_ingredients.ingredient_id
  = ingredients.id
where meal_ingredients.meal_id = :'meal_id';

select
  nutrients.id as nutrient_id,
  nutrients.display_name as nutrient,
  nutrients.nutrient_category_id as nutrient_category_id,
  nutrients.precision as precision,
  round(sum((ingredient_nutrients.amount_per_100g / 100) * meal_ingredients.mass_in_grams), 3) as amount,
  units.name as unit,
  round(sum(ingredient_nutrients.amount_per_100g * meal_ingredients.mass_in_grams / nullif(intake_guideline_nutrients.rdi, 0)), 2) as pdv
from ingredient_nutrients
inner join meal_ingredients
  on ingredient_nutrients.ingredient_id
  = meal_ingredients.ingredient_id
  and meal_ingredients.meal_id
  = :'meal_id'
inner join nutrients
  on nutrients.id
  = ingredient_nutrients.nutrient_id  
inner join units
  on units.id
  = nutrients.unit_id
inner join intake_guideline_nutrients
  on intake_guideline_nutrients.intake_guideline_id
  = :'intake_guideline_id'
  and intake_guideline_nutrients.nutrient_id
  = ingredient_nutrients.nutrient_id
group by nutrients.id, units.name
order by nutrients.display_order_id;
