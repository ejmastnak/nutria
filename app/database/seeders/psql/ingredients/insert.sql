-- Populates ingredient-related tables
-- Ingredient categories{{{
insert into ingredient_categories (
  id,
  name
)
select
  id::int,
  name
from sr.food_category;
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
  fdc_id::int,
  description,
  ingredient_category_id::int,
  (now() at time zone 'utc'),
  (now() at time zone 'utc')
from sr.food;
/*}}}*/
