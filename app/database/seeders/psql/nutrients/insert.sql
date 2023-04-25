-- Populates the nutrients table.
-- Prerequisite: the units and nutrient_categories tables are populated.
insert into nutrients(
  id,
  name,
  display_name,
  unit_id,
  nutrient_category_id
)
select
  sr.nutrient.id::int,
  sr.nutrient.name,
  tmp_nutrients.display_name,
  units.id,
  nutrient_categories.id
from sr.nutrient
inner join tmp_nutrients
  on tmp_nutrients.name
  like sr.nutrient.name
  and tmp_nutrients.id
  = sr.nutrient.id::int
inner join units
  on units.name
  ilike sr.nutrient.unit_name
inner join nutrient_categories
  on nutrient_categories.name
  ilike tmp_nutrients.nutrient_category_name;

-- Manually add nutrient-specific units{{{
-- UG RAE for Vitamin A
-- "1106", "Vitamin A, RAE"
update nutrients
set unit_id = (select id from units where name ilike 'UG RAE')
where nutrients.name ilike 'Vitamin A, RAE';

-- UG DFE for Folate
-- "1190", "Folate, DFE"
update nutrients
set unit_id = (select id from units where name ilike 'UG DFE')
where nutrients.name ilike 'Folate, DFE';

-- MG NE for Niacin
-- "1167", "Niacin"
update nutrients
set unit_id = (select id from units where name ilike 'MG NE')
where nutrients.name ilike 'Niacin';
/*}}}*/
