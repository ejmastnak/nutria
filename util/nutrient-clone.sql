-- Solves this problem: When cloning ingredients for ingredients from FDA database which don't have all nutrients present, you need to add the missing nutrients and set their `amount_per_100_g` values to zero.

\set ingredient_id 1
\set rdi_profile_id 1

-- Print ingredient name
select id, name from ingredients where id = :'ingredient_id';

select
  nutrients.id as nutrient_id,
  nutrients.display_name as display_name,
  coalesce(round(ingredient_nutrients.amount_per_100g, 2),0) as amount_per_100g,
  nutrients.nutrient_category_id as nutrient_category_id,
  units.id as unit_id,
  units.name as unit_name
from nutrients
left join ingredient_nutrients
  on ingredient_nutrients.nutrient_id  
  = nutrients.id
  and ingredient_nutrients.ingredient_id
  = :'ingredient_id'
inner join units
  on units.id
  = nutrients.unit_id
order by nutrients.display_order_id;
