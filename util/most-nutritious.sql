-- Prints N ingredients with highest values of a selected nutrient

\set N 200
\set nutrient_id 1092

select ingredients.name, ingredient_nutrients.amount_per_100g
from ingredients
inner join ingredient_nutrients
  on ingredients.id
  = ingredient_nutrients.ingredient_id
  and ingredient_nutrients.nutrient_id=:'nutrient_id'
order by amount_per_100g desc limit :'N';
