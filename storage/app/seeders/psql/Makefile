DB=diet
DB_USER=diet

# Main build script to set up database
MAIN=$(CURDIR)/main.sql

# Auxiliary SQL scripts
SR_SQL=$(CURDIR)/sql/sr.sql
TMP_UNITS_SQL=$(CURDIR)/sql/tmp-units.sql
INGREDIENTS_SQL=$(CURDIR)/sql/ingredients.sql
INGREDIENT_NUTRIENTS_SQL=$(CURDIR)/sql/ingredient-nutrients.sql
FOOD_PORTION_SQL=$(CURDIR)/sql/food-portion.sql
DENSITY_CANDIDATES_SQL=$(CURDIR)/sql/density-candidates.sql
DENSITY_WHITELIST_SQL=$(CURDIR)/sql/density-whitelist.sql
CUSTOM_UNITS_SQL=$(CURDIR)/sql/custom-units.sql
DENSITY_SQL=$(CURDIR)/sql/density.sql

DENSITY_CANDIDATES_TXT=$(CURDIR)/density-candidates.txt

# Create and populates SR tables
.PHONY: sr
sr:
	@echo "" >> $(MAIN)
	@echo "" >> $(MAIN)
	@cat $(SR_SQL) >> $(MAIN)

# Creates and populates temporary unit-related tables
.PHONY: tmp_units
tmp_units:
	@cat $(TMP_UNITS_SQL) >> $(MAIN)

# Creates and populates temporary density whitelist table
.PHONY: whitelist
whitelist:
	@cat $(DENSITY_WHITELIST_SQL) >> $(MAIN)

# Populates ingredient table
.PHONY: ingredients
ingredients: sr
	@echo "" >> $(MAIN)
	@echo "" >> $(MAIN)
	@cat $(INGREDIENTS_SQL) >> $(MAIN)

# Populates ingredient_nutrients table
.PHONY: ingredient_nutrients
ingredient_nutrients: ingredients
	@echo "" >> $(MAIN)
	@echo "" >> $(MAIN)
	@cat $(INGREDIENT_NUTRIENTS_SQL) >> $(MAIN)

# Populates and preprocesses the food_portion table
.PHONY: food_portion
food_portion: tmp_units ingredients
	@echo "" >> $(MAIN)
	@echo "" >> $(MAIN)
	@cat $(FOOD_PORTION_SQL) >> $(MAIN)

# Selects a list of candidate ingredients for computing density
.PHONY: density-candidates
density-candidates: food_portion
	@echo "" >> $(MAIN)
	@echo "" >> $(MAIN)
	@cat $(DENSITY_CANDIDATES_SQL) >> $(MAIN)

# Updates ingredients table with ingredient density
.PHONY: density
density: tmp_units food_portion whitelist
	@echo "" >> $(MAIN)
	@echo "" >> $(MAIN)
	@cat $(DENSITY_SQL) >> $(MAIN)

# Updates ingredients table with ingredient density
.PHONY: custom_units
custom_units: tmp_units food_portion whitelist
	@echo "" >> $(MAIN)
	@echo "" >> $(MAIN)
	@cat $(CUSTOM_UNITS_SQL) >> $(MAIN)

# Creates a list of candidate ingredients for computing density
density-candidates.txt: clean density-candidates
	psql --quiet -d $(DB) -U $(DB_USER) -f $(MAIN) > $(DENSITY_CANDIDATES_TXT)

# Creates seed script
main.sql: clean ingredient_nutrients density custom_units;

# Runs seed script
.PHONY: seed
seed: main.sql
	psql -d $(DB) -U $(DB_USER) -f $(MAIN)

# Removes seed script
clean:
	@rm -rf $(MAIN)
