-- Populates the `nutrients` table.
-- Prerequisite: the `units` table is populated.
insert into nutrients(
  id,
  name,
  display_name,
  unit_id
)
select
  sr.nutrient.id::int,
  sr.nutrient.name,
  tmp_nutrient_whitelist.display_name,
  units.id
from sr.nutrient
inner join tmp_nutrient_whitelist
  on tmp_nutrient_whitelist.name
  like sr.nutrient.name
  and tmp_nutrient_whitelist.id
  = sr.nutrient.id::int
inner join units
  on units.name
  ilike sr.nutrient.unit_name;

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
