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
  ingredient_category_id
)
select
  fdc_id::int,
  description,
  ingredient_category_id::int
from sr.food;
/*}}}*/
