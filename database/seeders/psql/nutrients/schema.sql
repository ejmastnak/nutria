-- Creates table structure for a version of nutrient table ready for import
-- into Laravel project
drop table if exists nutrients cascade;

create table nutrients (
  id integer primary key,  -- use FDC id from SR database
  name text not null,
  display_name text not null,
  unit_id integer not null references units(id)
);

create temporary table tmp_nutrient_whitelist (
  id integer,  -- use FDC id from SR database
  name text,
  display_name text
);
