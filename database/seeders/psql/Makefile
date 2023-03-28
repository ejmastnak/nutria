DB=diet
DB_USER=diet
SEP=--------------------------------------------------------------------------------

# Main build script to set up database
SEED=${CURDIR}/seed.sql

SR_SCHEMA=$(CURDIR)/sr/schema.sql
SR_NUTRIENT_CSV=$(CURDIR)/sr/nutrient.csv
SR_FOOD_CATEGORY_CSV=$(CURDIR)/sr/food_category.csv
SR_FOOD_CSV=$(CURDIR)/sr/food.csv
SR_FOOD_NUTRIENT_CSV=$(CURDIR)/sr/food_nutrient.csv

# Units
UNITS_SCHEMA=$(CURDIR)/units/schema.sql
UNITS_CSV=$(CURDIR)/units/units.csv

# Nutrients
NUTRIENTS_WHITELIST_CSV=$(CURDIR)/nutrients/whitelist.csv
NUTRIENTS_SCHEMA=$(CURDIR)/nutrients/schema.sql
NUTRIENTS_INSERT=$(CURDIR)/nutrients/insert.sql

# Ingredients
INGREDIENTS_SCHEMA=$(CURDIR)/ingredients/schema.sql
INGREDIENTS_INSERT=$(CURDIR)/ingredients/insert.sql

# Ingredient nutrients
INGREDIENT_NUTRIENTS_SCHEMA=$(CURDIR)/ingredient-nutrients/schema.sql
INGREDIENT_NUTRIENTS_INSERT=$(CURDIR)/ingredient-nutrients/insert.sql


# Imports SR tables into sr schema
.PHONY: import_sr
import_sr:
	@echo $(SEP) >> $(SEED)
	@echo "-- Create and populate sr schema" >> $(SEED)
	@echo $(SEP) >> $(SEED)
	@cat $(SR_SCHEMA) >> $(SEED)
	@echo "" >> $(SEED)
	@echo "-- Import SR tables from CSV" >> $(SEED)
	@echo "\COPY sr.nutrient FROM $(SR_NUTRIENT_CSV) WITH DELIMITER ',' HEADER CSV;" >> $(SEED)
	@echo "\COPY sr.food_category FROM $(SR_FOOD_CATEGORY_CSV) WITH DELIMITER ',' HEADER CSV;" >> $(SEED)
	@echo "\COPY sr.food FROM $(SR_FOOD_CSV) WITH DELIMITER ',' HEADER CSV;" >> $(SEED)
	@echo "\COPY sr.food_nutrient FROM $(SR_FOOD_NUTRIENT_CSV) WITH DELIMITER ',' HEADER CSV;" >> $(SEED)

# Seeds unit, ingredient_category, ingredient, nutrient, and
# ingredient_nutrient tables in the public schema
.PHONY: seed_public
seed_public: import_sr
	@echo "" >> $(SEED)
	@echo $(SEP) >> $(SEED)
	@echo "-- Seed ingredient and nutrient tables" >> $(SEED)
	@echo $(SEP) >> $(SEED)
	# -------------------------------------------------------------------------- #
	# Units
	# -------------------------------------------------------------------------- #
	@echo "" >> $(SEED)
	@echo $(SEP) >> $(SEED)
	@echo "-- Create and populate units table" >> $(SEED)
	@echo $(SEP) >> $(SEED)
	@cat $(UNITS_SCHEMA) >> $(SEED)
	@echo "" >> $(SEED)
	@echo "\COPY units (name, longname, is_mass, is_volume) FROM $(UNITS_CSV) WITH DELIMITER ',' HEADER CSV;" >> $(SEED)
	# -------------------------------------------------------------------------- #
	# Nutrients
	# -------------------------------------------------------------------------- #
	@echo "" >> $(SEED)
	@echo $(SEP) >> $(SEED)
	@echo "-- Create and populate nutrient-related tables" >> $(SEED)
	@echo $(SEP) >> $(SEED)
	@cat $(NUTRIENTS_SCHEMA) >> $(SEED)
	@echo "" >> $(SEED)
	@echo "\COPY tmp_nutrient_whitelist FROM $(NUTRIENTS_WHITELIST_CSV) WITH DELIMITER ',' HEADER CSV;" >> $(SEED)
	@echo "" >> $(SEED)
	@cat $(NUTRIENTS_INSERT) >> $(SEED)
	# -------------------------------------------------------------------------- #
	# Ingredients
	# -------------------------------------------------------------------------- #
	@echo "" >> $(SEED)
	@echo $(SEP) >> $(SEED)
	@echo "-- Create and populate ingredient-related tables" >> $(SEED)
	@echo $(SEP) >> $(SEED)
	@cat $(INGREDIENTS_SCHEMA) >> $(SEED)
	@echo "" >> $(SEED)
	@cat $(INGREDIENTS_INSERT) >> $(SEED)
	# -------------------------------------------------------------------------- #
	# Ingredients nutrients
	# -------------------------------------------------------------------------- #
	@echo "" >> $(SEED)
	@echo $(SEP) >> $(SEED)
	@echo "-- Create and populate ingredient_nutrients table" >> $(SEED)
	@echo $(SEP) >> $(SEED)
	@cat $(INGREDIENT_NUTRIENTS_SCHEMA) >> $(SEED)
	@echo "" >> $(SEED)
	@cat $(INGREDIENT_NUTRIENTS_INSERT) >> $(SEED)

# Creates seed script
seed.sql: clean seed_public;

# Runs seed script
.PHONY: seed
seed: seed.sql
	psql -d $(DB) -U $(DB_USER) -f $(SEED)

# Removes seed script
clean:
	@rm -rf $(SEED)
