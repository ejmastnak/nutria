-- Populates and indexes the ingredient_nutrients table.
-- Prerequisite: `nutrient` and `ingredient` are populated.
insert into ingredient_nutrients (
  ingredient_id,
  nutrient_id,
  amount_per_100g
)
select
  ingredients.id,  -- use local ingredient id and not FDC id
  sr.food_nutrient.nutrient_id::int,
  sr.food_nutrient.amount_per_100g::decimal(10, 3)
from sr.food_nutrient
inner join ingredients  -- to access local ingredient id
  on ingredients.fdc_id
  = sr.food_nutrient.fdc_id::int
inner join nutrients
  on nutrients.id
  = sr.food_nutrient.nutrient_id::int;

create index idx_ingredient_nutrients on ingredient_nutrients(ingredient_id, nutrient_id);
