-------------------------------------------------------------------------------
-- Creates and populates temporary ingredient whitelist table
-- Prerequisite: ingredient whitelist tsv file exists
-------------------------------------------------------------------------------
create temporary table tmp_ingredient_whitelist(
  is_whitelisted text,
  fdc_id integer,
  ingredient_category_name text,
  ingredient_name text
);

\COPY tmp_ingredient_whitelist (is_whitelisted, fdc_id, ingredient_category_name, ingredient_name) FROM ./csv/ingredient-whitelist.csv WITH DELIMITER ',' HEADER CSV;

-- Only keep whitelisted ingredients
delete from tmp_ingredient_whitelist where is_whitelisted not like '1';
-------------------------------------------------------------------------------
