User Interface
==============

Home
----

Actions:

- View nutrient profiles
- Create stuff

CRUD
----

Home
^^^^

Links to CRUD pages for:

- Ingredients
- Meals
- Food lists
- RDI profile

Ingredient CRUD
^^^^^^^^^^^^^^^

**Index**

- Ingredient name (links to Show page) and category ``ingredient_category_id`` (maybe) for each ingredient
- Filter by: name and category

**Show**

- Ingredient name
- Category ``ingredient_category_id`` (maybe)
- Density ``grams_per_milliliter`` (if applicable)
- Nutrient profile for 100 grams of ingredient

**Create**

- "Clone from existing ingredient" button
- Form satisfying specification in :ref:`Features: Create an Ingredient <feature-create-ingredient>`
- Save button
- Save and profile button
- Cancel button (back)

**Edit**

- Form satisfying specification in :ref:`Features: Create an Ingredient <feature-create-ingredient>`, prefilled with current values.
- Save button
- Save and profile button
- Cancel button (back)

Meal CRUD
^^^^^^^^^

**Index**

- Meal name (links to Show page) for each meal
- Filter by: name

**Show**

- Meal name
- Name, amount, and unit (in originally specified units) of each ingredient
- "Compute nutrient profile" button for default quantity of meal
  (Or just show nutrient profile if it's inexpensive to compute)

**Create**

- "Clone from existing meal" button
- Form satisfying specification in :ref:`Features: Create a Meal <feature-create-meal>`
- Save button
- Save and profile button
- Cancel button (back)

**Edit**

- Form satisfying specification in :ref:`Features: Create a Meal <feature-create-meal>`, prefilled with current values
- Save button
- Save and profile button
- Cancel button (back)

Food List CRUD
^^^^^^^^^^^^^^

**Index**

- Food list name (links to Show page) for each food list
- Filter by: name

**Show**

- Food list name
- Nutrient name, rdi amount, and rdi unit (in nutrient's preferred unit) of each RDI profile nutrient

**Create**

- "Clone from existing RDI profile" button
- Form satisfying specification in :ref:`Features: Create an RDI Profile <feature-create-rdi-profile>`
- Save button
- Save and profile button
- Cancel button (back)

**Edit**

- Form satisfying specification in :ref:`Features: Create an RDI Profile <feature-create-rdi-profile>`, prefilled with current values
- Save button
- Save and profile button
- Cancel button (back)

RDI Profile CRUD
^^^^^^^^^^^^^^^^

**Index**

- RDI profile name (links to Show page) for each RDI profile
- Filter by: name

**Show**

- RDI profile name
- Name, amount, and unit (in originally specified units) of each food list item
- "Compute nutrient profile" button
  (Or just show nutrient profile if it's inexpensive to compute)

**Create**

- "Clone from existing food list" button
- Form satisfying specification in :ref:`Features: Create a Food List <feature-create-food-list>`
- Save button
- Save and profile button
- Cancel button (back)

**Edit**

- Form satisfying specification in :ref:`Features: Create a Food List <feature-create-food-list>`, prefilled with current values
- Save button
- Save and profile button
- Cancel button (back)

Nutrient profiles
-----------------

Home: Nutrient profiles
^^^^^^^^^^^^^^^^^^^^^^^

View nutrient profile of:

- An ingredient
- A meal
- A food list

Profile an ingredient
^^^^^^^^^^^^^^^^^^^^^

- Which ingredient? (fuzzy search over ingredient)
- How much?
  Text input for amount.
  Select for unit, with unit options dependent of ingredient supporting density.
- RDI profile to use for computing PDV.

Actions

- "Compute profile" button
- "Cancel" button (redirect back)
- Create a "New ingredient" button

Profile a meal
^^^^^^^^^^^^^^

- Which meal? (fuzzy search over meal)
- How much?
  Text input for mass.
  Select for unit.
  Default mass by default.
- RDI profile to use for computing PDV.

Actions

- "Compute profile" button
- "Cancel" button (redirect back)
- Create a "New meal" button

Profile a food list
^^^^^^^^^^^^^^^^^^^

- Which foods? (fuzzy search over meals and ingredients)
  (Or use a new food list).
- How much?
  Text input for amount.
  Select for unit, with possible units dependent on ingredient supporting density.
  Default mass for meals by default.
- RDI profile to use for computing PDV.

Actions

- "Compute profile" button
- "Cancel" button (redirect back)
- Create a "New food list" button
