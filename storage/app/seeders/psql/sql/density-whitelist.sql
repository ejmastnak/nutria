-------------------------------------------------------------------------------
-- Creates and populates temporary density whitelist table
-- Prerequisite: density whitelist csv file exists
-------------------------------------------------------------------------------
create temporary table tmp_density_whitelist(
  food_portion_id integer,
  fdc_id integer,
  ingredient_name text
);

\COPY tmp_density_whitelist (food_portion_id, fdc_id, ingredient_name) FROM ./csv/density-whitelist.csv WITH DELIMITER ',' HEADER CSV;
-------------------------------------------------------------------------------
