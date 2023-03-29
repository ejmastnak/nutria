-- Creates table structure for normalized version of ingredient-related tables
drop table if exists ingredient_categories cascade;
drop table if exists ingredients cascade;

create table if not exists ingredient_categories (
  id integer primary key, -- use id from SR database
  name text not null
);

create table if not exists ingredients (
  id int primary key generated always as identity,
  fdc_id int null,
  name text not null,
  ingredient_category_id integer null references ingredient_categories(id),
  grams_per_milliliter decimal(10, 6) null
);
