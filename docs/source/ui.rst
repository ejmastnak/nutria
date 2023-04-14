User Interface
==============

Home
----

Actions:

- View nutrient profiles
- Create stuff

CRUD Home
---------

This is the home page for CRUD actions on Ingredients, Meals, Food Lists, and RDI Profiles.

Links to CRUD pages for:

- Ingredients
- Meals
- Food lists
- RDI profile

Ingredient CRUD
---------------

Index
^^^^^

- Ingredient name (links to Show page) and category ``ingredient_category_id`` (maybe) for each ingredient
- Filter by: name and category

Show
^^^^

- Ingredient name
- Category ``ingredient_category_id`` (maybe)
- Density ``density_g_per_ml`` (if applicable)
- Nutrient profile for 100 grams of ingredient

Create
^^^^^^

- "Clone from existing ingredient" button
- Form satisfying specs in Ingredient Create validation.
- Save button
- Save and profile button
- Cancel button (back)

Edit
^^^^

- Form satisfying specs in Ingredient Update validation.
- Save button
- Save and profile button
- Cancel button (back)

Meal CRUD
---------

Index
^^^^^

- Meal name (links to Show page) for each meal
- Filter by: name

Show
^^^^

- Meal name
- Name, amount, and unit (in originally specified units) of each meal ingredient
- "Compute nutrient profile" button for default quantity of meal
  (Or just show nutrient profile if it's inexpensive to compute)

Create
^^^^^^

- "Clone from existing meal" button
- Form satisfying specs in Meal Create validation.
- Save button
- Save and profile button
- Cancel button (back)

Edit
^^^^

- Form satisfying specs in Meal Update validation.
- Save button
- Save and profile button
- Cancel button (back)

Food List CRUD
--------------

Index
^^^^^

- Food list name (links to Show page) for each food list
- Filter by: name

Show
^^^^

- Food list name
- Name, amount, and unit (in originally specified units) of all food list ingredients
- Name, amount, and unit (in originally specified units) of all food list meals
- Nutrient profile of food list

Create
^^^^^^

- "Clone from existing food list profile" button
- Form satisfying specs in Food List Create validation.
- Save button
- Save and profile button
- Cancel button (back)

Edit
^^^^

- Form satisfying specs in Food List Update validation.
- Save button
- Save and profile button
- Cancel button (back)

RDI Profile CRUD
----------------

Index
^^^^^

- RDI profile name (links to Show page) for each RDI profile
- Filter by: name

Show
^^^^

- RDI profile name
- Name, RDI value, and unit (in nutrient's preferred units) of each RdiProfileNutrient

Create
^^^^^^

- "Clone from existing food list" button
- Form satisfying specs in RDI Profile Create validation.
- Save button
- Save and profile button
- Cancel button (back)

Edit
^^^^

- Form satisfying specs in RDI Profile Update validation.
- Save button
- Save and profile button
- Cancel button (back)
