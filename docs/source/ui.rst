User Interface
==============

Home
----

Actions:

- View nutrient profiles
- Create stuff

Create stuff
------------

Home: Create stuff
^^^^^^^^^^^^^^^^^^

Create/edit:

- Ingredients
- Meals
- Food lists
- RDI profile

Each of ingredients, meals, and food lists Create and Edit pages should have both "Save" and (immediately) "Save and profile".

Each Create page should have an option to clone an existing model instance, e.g. create new ingredient based on existing ingredient (nutrient values prefilled, then just tweak a few values). 

CRUDs
^^^^^

Ingredients, Meals, Food lists, and RDI profile I think can all be standard CRUD with Index, Create/Edit.
Similar to e.g. Landmark CRUD.

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
