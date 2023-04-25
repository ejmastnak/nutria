Database
========

Goal: describe the app's database structure.

Tables
------

- ``units``
- ``ingredient_categories``
- ``nutrients``
- ``ingredients``
- ``ingredient_nutrients``
- ``meals``
- ``meal_ingredients``
- ``food_lists``
- ``food_list_ingredients``
- ``food_list_meals``
- ``rdi_profiles``
- ``rdi_profile_nutrients``

The ``units``, ``ingredient_categories``, and ``nutrients`` tables are meant to be constant.
The user can edit the other tables.

Unit
^^^^

- ``name``
- ``longname``
- ``is_mass``
- ``is_volume``

Nutrient category
^^^^^^^^^^^^^^^^^

- ``name``

Nutrient
^^^^^^^^

- ``name``
- ``display_name``
- ``unit_id``
- ``nutrient_category_id``
- ``display_order_id``

Ingredient category
^^^^^^^^^^^^^^^^^^^

- ``name``

Ingredient
^^^^^^^^^^

- ``fdc_id``
- ``name``
- ``ingredient_category_id``
- ``density_g_per_ml``

Ingredient Nutrient
^^^^^^^^^^^^^^^^^^^

Records how much of a given nutrient occurs in a given ingredient.

- ``ingredient_id``
- ``nutrient_id``
- ``amount_per_100g``

Meal
^^^^

- ``name``
- ``mass_in_grams``

Meal Ingredient
^^^^^^^^^^^^^^^

Records how much of a given ingredient occurs in a meal.

- ``meal_id``
- ``ingredient_id``
- ``amount``
- ``unit_id``
- ``mass_in_grams``

Food List
^^^^^^^^^

- ``name``
- ``mass_in_grams``

Food List Ingredient
^^^^^^^^^^^^^^^^^^^^

Records how much of a given ingredient occurs in a food list.

- ``food_list_id``
- ``ingredient_id``
- ``amount``
- ``unit_id``
- ``mass_in_grams``

Food List Meal
^^^^^^^^^^^^^^

Records how much of a given meal occurs in a food list.

- ``food_list_id``
- ``meal_id``
- ``amount``
- ``unit_id``
- ``mass_in_grams``

RDI Profile
^^^^^^^^^^^

- ``name``

RDI Profile Nutrient
^^^^^^^^^^^^^^^^^^^^

Represents the Reference Daily Intake for a given nutrient in a given RDI profile.

- ``rdi_profile_id``
- ``nutrient_id``
- ``rdi``

Data Sources
------------

The source is the FDA's FoodData Central Standard Reference Legacy Database.

Description of FoodData Central databases: https://fdc.nal.usda.gov/faq.html#q3

Data used in this app is available at https://fdc.nal.usda.gov/download-datasets.html:

- `SR Legacy - April 2019 (CSV – 6.1MB) <https://fdc.nal.usda.gov/fdc-datasets/FoodData_Central_sr_legacy_food_csv_%202019-04-02.zip>`_
- `Supporting data for CSV Downloads - April 2019 (CSV – 210K) <https://fdc.nal.usda.gov/fdc-datasets/FoodData_Central_Supporting_Data_csv_%202019-04-02.zip>`_
