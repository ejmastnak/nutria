#!/bin/bash
# Helper function for running SQL scripts
# SYNOPSIS
#     run.bash script.sql

if [[ $# -ne 1 ]]; then
  exit
fi

export PGPASSWORD=diet
psql -U diet -d diet -f "${1}"
