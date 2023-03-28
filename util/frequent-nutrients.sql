-- Prints nutrients that occur at least N times in ingredient_nutrients.
-- Potentially also saves resulting to nutrients to local CSV file.

-- FYI there are 7793 foods in FoodData Central SR Legacy database
\set N 1000

-- Nutrient id, nutrient name, nutrient display name, unit
create temporary view nutrient_view as
select distinct ingredient_nutrients.nutrient_id as id, nutrients.name as name, nutrients.display_name as display_name, units.name as unit
from ingredient_nutrients
inner join nutrients
on nutrients.id = ingredient_nutrients.nutrient_id
inner join units on units.id = nutrients.unit_id
group by ingredient_nutrients.nutrient_id, nutrients.name, nutrients.display_name, units.name
having count(ingredient_nutrients.nutrient_id) > :'N'
order by ingredient_nutrients.nutrient_id;

select * from nutrient_view;

-- \copy (select * from nutrient_view) to 'frequent-nutrients.ssv' with csv delimiter ';' header
