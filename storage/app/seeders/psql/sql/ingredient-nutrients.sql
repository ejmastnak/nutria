-------------------------------------------------------------------------------
-- Populates ingredient_nutrients table
-- Prerequisite: ingredients, nutrients, and sr tables is populated.
-------------------------------------------------------------------------------
truncate table ingredient_nutrients restart identity;

-- Add an ingredient_nutrient record for every nutrient; use
-- sr.food_nutrient.amount_per_100g when available, and zero otherwise (hence
-- coalesce and cross/left joins).
insert into ingredient_nutrients (
  ingredient_id,
  nutrient_id,
  amount,
  amount_per_100g
)
select
  ingredients.id,  -- use local ingredient id and not FDC id
  nutrients.id,
  coalesce(sr.food_nutrient.amount_per_100g::decimal(10, 3), 0.0),
  coalesce(sr.food_nutrient.amount_per_100g::decimal(10, 3), 0.0)
from ingredients
cross join nutrients
left join sr.food_nutrient
  on sr.food_nutrient.fdc_id::int
  = ingredients.fdc_id
  and sr.food_nutrient.nutrient_id::int
  = nutrients.id;
-------------------------------------------------------------------------------
