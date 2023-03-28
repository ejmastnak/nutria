-- Creates sr schema and table structure for tables from SR Legacy database
-- in preparation for importing these tables from CSV files.
drop schema if exists sr cascade;
create schema sr;

create table sr.nutrient(
  id text,
  name text,
  unit_name text,
  nutrient_nbr text,
  rank text
);

create table sr.food_category(
  id text,
  code text,
  name text
);

create table sr.food(
  fdc_id text,
  data_type text,
  description text,
  ingredient_category_id text,
  publication_date text
);

create table sr.food_nutrient(
  id text,
  fdc_id text,
  nutrient_id text,
  amount_per_100g text,
  data_points text,
  derivation_id text,
  min text,
  max text,
  median text,
  footnote text,
  min_year_acquired text
);
