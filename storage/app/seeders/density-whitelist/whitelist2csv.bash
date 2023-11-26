#!/bin/bash
# Converts human-readable SQL output to SQL-parseable CSV file
# Note to self: prepare density-candidates.txt by running
# density-candidates.txt target in Makefile, manually look through
# density-candidates.txt and retain ingredients for which you want to compute a
# density, then pass through whitelist2csv.bash to get density-whitelist.csv.
# Warning: ingredient names may contain single quotes, double quotes, and
# commas.

IN="density-whitelist.txt"
TMP="density-whitelist.csv.tmp"
OUT="density-whitelist.csv"
HEADER="food_portion_id|fdc_id|ingredient_category_name|ingredient_name"

echo "${HEADER}" > ${TMP}

# Escape quotation marks
sed 's/"/""/g' ${IN} >> ${TMP}

# Switch to CSV delimiter and add quotes around ingredient field
awk -F '|' '{printf "%s,%s,\"%s\"\n",$1,$2,$4}' ${TMP} > ${OUT}
rm -f ${TMP}
