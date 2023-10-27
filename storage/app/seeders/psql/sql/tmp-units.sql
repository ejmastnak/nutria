-------------------------------------------------------------------------------
-- Creates and populates temporary, auxiliary unit-related tables
-- Prerequisite: units table is populated.
-------------------------------------------------------------------------------
create temporary table tmp_mass_units(name text);

create temporary table tmp_volume_units(
  name text,
  cname text,
  ml double precision,
  unit_id integer
);

-- Import unit-related tables from CSV
\COPY tmp_volume_units (name, cname, ml) FROM ./csv/volume_units.csv WITH DELIMITER ',' HEADER CSV;
\COPY tmp_mass_units FROM ./csv/mass_units.csv WITH DELIMITER ',' HEADER CSV;

-- Populates unit_id column in tmp_volume_units
update tmp_volume_units set unit_id = (select id from units where units.name like tmp_volume_units.cname limit 1);
-------------------------------------------------------------------------------
