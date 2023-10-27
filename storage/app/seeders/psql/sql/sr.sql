--------------------------------------------------------------------------------
-- Create and populate SR schema
--------------------------------------------------------------------------------
drop schema if exists sr cascade;
create schema sr;

create table sr.food(
  fdc_id text,
  data_type text,
  description text,
  food_category_id text,
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

create table sr.food_portion(
  id int,
  fdc_id text,
  seq_num text,
  amount text,
  measure_unit_id text,
  portion_description text,
  modifier text,
  gram_weight text,
  data_points text,
  footnote text,
  min_year_acquired text
);

-- Import SR tables from CSV
\COPY sr.food FROM ./csv/food.csv WITH DELIMITER ',' HEADER CSV;
\COPY sr.food_nutrient FROM ./csv/food_nutrient.csv WITH DELIMITER ',' HEADER CSV;
\COPY sr.food_portion FROM ./csv/food_portion.csv WITH DELIMITER ',' HEADER CSV;
--------------------------------------------------------------------------------
