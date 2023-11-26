#!/bin/bash
# Intermediate step in pipeline for creating ingredient whitelist. This step
# converts human-prepared ingredient whitelist to a SQL-parsable CSV file.
# Warning: ingredient names may contain single quotes, double quotes, and
# commas.

IN="ingredient-whitelist.txt"
TMP="ingredient-whitelist.csv.tmp"
OUT="ingredient-whitelist.csv"

# The number of sed passes is terribly inefficient I'm sure, but eh.

# Delete empty lines
sed '/^\s*$/d' ${IN} > ${TMP}

# Replace ' | ' with '|', so that I can use '|' as AWK delimiter.
sed -i 's/ | /|/g' ${TMP}

# Escape quotation marks
sed -i 's/"/""/g' ${TMP}

# Switch to CSV delimiter and add quotes around each CSV field
awk -F '|' '{printf "\"%s\",\"%s\",\"%s\",\"%s\"\n",$1,$2,$3,$4}' ${TMP} > ${OUT}
rm -f ${TMP}
