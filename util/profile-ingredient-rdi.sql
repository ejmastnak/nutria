-- Display amount and PDV for nutrients in N grams of a given ingredient

\timing on
\set ingredient_id 6435
\set intake_guideline_id 1

-- Print ingredient name
select id, name from ingredients where id = :'ingredient_id';

select
  nutrients.id as nutrient_id,
  nutrients.display_name as nutrient,
  nutrients.nutrient_category_id as nutrient_category_id,
  nutrients.precision as precision,
  round(ingredient_nutrients.amount_per_100g, 3) as amount,
  units.name as unit,
  round((ingredient_nutrients.amount_per_100g / nullif(intake_guideline_nutrients.rdi, 0)) * 100, 2) as pdv
from ingredient_nutrients
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
where ingredient_nutrients.ingredient_id=:'ingredient_id'
order by nutrients.display_order_id;
