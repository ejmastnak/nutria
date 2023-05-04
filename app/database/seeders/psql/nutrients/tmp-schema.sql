-- Creates table structure for a version of nutrient table ready for import
-- into Laravel project
create temporary table tmp_nutrients (
  id integer,  -- use FDC id from SR database
  name text,
  display_name text,
  nutrient_category_name text,
  precision integer,
  display_order_id integer
);
