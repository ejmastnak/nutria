-------------------------------------------------------------------------------
-- Populates ingredients table
-- Prerequisite: ingredient_categories and sr tables are populated.
-------------------------------------------------------------------------------
delete from units where ingredient_id is not null;
alter table units drop constraint units_ingredient_id_foreign;
truncate table ingredients restart identity cascade;
alter table units add constraint units_ingredient_id_foreign foreign key (ingredient_id) references ingredients(id);

-- Populates ingredients table.
-- Ingredients
insert into ingredients (
  fdc_id,
  name,
  ingredient_category_id,
  ingredient_nutrient_amount,
  ingredient_nutrient_amount_unit_id,
  created_at,
  updated_at
)
select
  sr.food.fdc_id::int,
  sr.food.description,
  sr.food.food_category_id::int,
  100,
  (select id from units where name like 'g' limit 1),
  (now() at time zone 'utc'),
  (now() at time zone 'utc')
from sr.food
inner join ingredient_categories
  on sr.food.food_category_id::int
  = ingredient_categories.id;
-------------------------------------------------------------------------------
