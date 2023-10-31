-------------------------------------------------------------------------------
-- Creates and populates temporary ingredient whitelist table
-- Prerequisite: ingredient whitelist tsv file exists
-------------------------------------------------------------------------------
create table tmp_ingredient_whitelist(
  include text,
  fdc_id integer,
  ingredient_category_name text,
  ingredient_name text
);

\COPY tmp_ingredient_whitelist (include, fdc_id, ingredient_category_name, ingredient_name) FROM ./csv/ingredient-whitelist.tsv WITH HEADER;

delete from tmp_ingredient_whitelist where include not like 'W';
-------------------------------------------------------------------------------
