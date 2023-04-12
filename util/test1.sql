\timing on
\set ingredient_id 6435
\set rdi_profile_id 1

select 
  j1.nutrient as nutrient,
  j1.amount + j2.amount as amount,
  j1.unit as unit,
  j1.pdv + j2.pdv || '%' as pdv
from (
  select
    nutrients.name as nutrient,
    round(ingredient_nutrients.amount_per_100g, 2) as amount,
    units.name as unit,
    round((ingredient_nutrients.amount_per_100g / nullif(rdi_profile_nutrients.rdi, 0)) * 100, 1) as pdv
  from ingredient_nutrients
  inner join nutrients
    on nutrients.id
    = ingredient_nutrients.nutrient_id  
  inner join units
    on units.id
    = nutrients.unit_id
  inner join rdi_profile_nutrients
    on rdi_profile_nutrients.rdi_profile_id
    = :'rdi_profile_id'
    and rdi_profile_nutrients.nutrient_id
    = ingredient_nutrients.nutrient_id
  where ingredient_nutrients.ingredient_id=:'ingredient_id'
  order by nutrients.id
) j1
join (
  select
    nutrients.name as nutrient,
    round(ingredient_nutrients.amount_per_100g, 2) as amount,
    units.name as unit,
    round((ingredient_nutrients.amount_per_100g / nullif(rdi_profile_nutrients.rdi, 0)) * 100, 1) as pdv
  from ingredient_nutrients
  inner join nutrients
    on nutrients.id
    = ingredient_nutrients.nutrient_id  
  inner join units
    on units.id
    = nutrients.unit_id
  inner join rdi_profile_nutrients
    on rdi_profile_nutrients.rdi_profile_id
    = :'rdi_profile_id'
    and rdi_profile_nutrients.nutrient_id
    = ingredient_nutrients.nutrient_id
  where ingredient_nutrients.ingredient_id=:'ingredient_id'
  order by nutrients.id
) j2 on j1.nutrient=j2.nutrient;
