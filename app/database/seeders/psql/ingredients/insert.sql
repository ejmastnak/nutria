-- Populates ingredient-related tables
-- Ingredient categories{{{
insert into ingredient_categories (
  id,
  name
)
select
  sr.food_category.id::int,
  sr.food_category.name
from sr.food_category
inner join tmp_ingredient_category_whitelist
  on tmp_ingredient_category_whitelist.name
  like sr.food_category.name
  and tmp_ingredient_category_whitelist.id
  = sr.food_category.id::int;
/*}}}*/

-- Ingredients{{{
insert into ingredients (
  fdc_id,
  name,
  ingredient_category_id,
  created_at,
  updated_at
)
select
  sr.food.fdc_id::int,
  sr.food.description,
  sr.food.food_category_id::int,
  (now() at time zone 'utc'),
  (now() at time zone 'utc')
from sr.food
inner join ingredient_categories
  on sr.food.food_category_id::int
  = ingredient_categories.id;
/*}}}*/
