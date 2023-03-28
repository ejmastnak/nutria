-- Creates table structure for normalized version of the ingredient_nutrients table
drop table if exists ingredient_nutrients cascade;

create table ingredient_nutrients (
  id int primary key generated always as identity,
  ingredient_id integer not null references ingredients(id) on delete cascade,
  nutrient_id integer not null references nutrients(id),
  amount_per_100g decimal(10, 3) not null -- measured in nutrient's preferred units
);
