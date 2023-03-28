-- Creates table structure for units table
drop table if exists units cascade;
create table units (
  id integer primary key generated always as identity,
  name text not null,
  longname text not null,
  is_mass boolean not null,
  is_volume boolean not null
);
