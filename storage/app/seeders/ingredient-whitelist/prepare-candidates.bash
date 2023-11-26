#!/bin/bash
# First step in pipeline for creating ingredient whitelist.
# This step outputs all ingredients to a text file in a format amenable to
# human parsing. The next step is for a human to look over each record and
# manually mark which rows (ingredients) should be whitelisted.

DB="tmp.sqlite"
CANDIDATES="ingredient-candidates.txt"
HEADER="is_whitelisted | fdc_id | ingredient_category | ingredient"

QUERY="select ' ', food.fdc_id, food_category.description, food.description from food inner join food_category on food.food_category_id = food_category.id order by food_category.description, food.description;"

# Create database
rm -f ${DB}
touch ${DB}

# Populate database
sqlite3 ${DB} -cmd \
  ".mode csv" \
  ".import food.csv food" \
  ".import food_category.csv food_category"

# Create candidate text file
echo "${HEADER}" > ${CANDIDATES}
sqlite3 ${DB} "${QUERY}" | sed 's/|/ | /g' >> ${CANDIDATES}
