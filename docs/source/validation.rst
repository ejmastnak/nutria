Validation
==========

.. _validation-crud-ingredient:

Create or Update Ingredient
---------------------------

**Incoming request**

.. code-block:: json

  {
    "name": "Foo",
    "ingredient_category_id": 0,
    "density_g_per_ml": null,
    "ingredient_nutrients": [
      {
        "id": 0,
        "nutrient_id": 0,
        "amount_per_100g": "0.0"
      }
    ]
  }

**Validatation**

- ``name`` is a required string with sane min and max length.
- ``ingredient_category_id``, is a required integer present in ``ingredient_categories,id``
- ``density_g_per_ml`` is either null or a positive float 
- ``ingredient_nutrients`` is a required array with exactly one entry for every Nutrient (distinct keys and length equal to ``Nutrients::count()``)  
- ``ingredient_nutrients.*.id`` is a required integer
- ``ingredient_nutrients.*.nutrient_id`` is a required integer present in ``nutrients,id`` with distinct values for every entry in ``ingredient_nutrients``
- ``ingredient_nutrients.*.amount_per_100g`` is a required nonnegative float

**Create**

- a new ``ingredient`` record with:

  - given ``name``
  - null ``fdc_id``
  - given ``ingredient_category_id``
  - given ``density_g_per_ml`` (possibly null)
  - ``user_id`` of user who made create request

- a new ``ingredient_nutrient`` record for each element in supplied ``nutrients`` with:

  - ``ingredient_id`` of ``ingredient`` record
  - given ``nutrient_id`` 
  - ``amount_per_100g``

**Update**

- Update existing ``ingredient`` record with given ``name``, ``fdc_id``, ``ingredient_category_id``, and ``density_g_per_ml``

- For each ``ingredient_nutrient`` element look up corresponding ``IngredientNutrient`` record from supplied ``ingredient_nutrient.*.id]`` and update

  - given ``nutrient_id`` 
  - given ``amount_per_100g``

.. _validation-crud-meal:

Create or Update Meal
---------------------

**Incoming request**

.. code-block:: json
    
  {
    "name": "Foo",
    "meal_ingredients": [
      {
        "id": 0,
        "ingredient_id": 0,
        "amount": 0.0,
        "unit_id": 0
      }
    ]
  }

**Validate**

- ``name`` is a string with sane min and max length.
- ``meal_ingredients`` is a required array with at least one item (and fewer than e.g. 1000 items)
- ``meal_ingredients.*.id`` is a required integer (you can't require that ``id`` exist in ``meal_ingredients,id`` because of new meal ingredients arriving from form)
- ``meal_ingredients.*.ingredient_id`` is a required integer present in ``ingredients,id``
- ``meal_ingredients.*.amount`` is a required positive float
- ``meal_ingredients.*.unit_id`` is a required integer present in ``units,id``

**Create**

- a new ``meal`` record with:
  
  - given ``name``
  - ``mass_in_grams`` initialized to zero
  - ``user_id`` of user who made create request

- a new ``meal_ingredient`` record for each element in supplied ``ingredients`` with:

  - ``meal_id`` of ``meal`` record
  - supplied ``ingredient_id``
  - supplied ``amount``
  - supplied ``unit_id``
  - ``mass_in_grams`` computed from supplied ``amount``, ``unit_id``, and potentially (for volume units) ``density_g_per_ml`` of ingredient specified by ``ingredient_id``
  - increment running sum of meal's ``mass_in_grams``

- set ``meal``'s ``mass_in_grams`` to sum of all ``meal_ingredient``'s ``mass_in_grams``

**Update**

- Update ``name`` column of existing ``meal`` record
- Temporarily reset ``meal``'s ``mass_in_grams`` to zero
- For all ``meal_ingredient`` objects that occur in both ``meal_ingredients`` DB table and in request (based on ``meal_ingredients.*.id`` value), update:

  - ``meal_id`` of ``meal`` record
  - supplied ``ingredient_id``
  - supplied ``amount``
  - supplied ``unit_id``
  - ``mass_in_grams`` computed from supplied ``amount``, ``unit_id``, and potentially (for volume units) ``density_g_per_ml`` of ingredient specified by ``ingredient_id``
  - increment running sum tracking meal's ``mass_in_grams``

- For all ``meal_ingredient`` objects in request and not in DB table, create a new ``meal_ingredient`` record with supplied values as in Create and increment running sum tracking meal's ``mass_in_grams``.

- Delete all ``meal_ingredient`` records in ``meal_ingredients`` DB table but not in request

- set ``meal``'s ``mass_in_grams`` to sum of all ``meal_ingredient``'s ``mass_in_grams``

.. _validation-crud-food-list:

Create or Update Food List
--------------------------

**Incoming request**

.. code-block:: json
  
  {
    "name": "Foo",
    "food_list_ingredients": [
      {
        "id": 0,
        "ingredient_id": 0,
        "amount": 0.0,
        "unit_id": 0
      }
    ],
    "food_list_meals": [
      {
        "id": 0,
        "meal_id": 0,
        "amount": 0.0,
        "unit_id": 0
      }
    ]
  }

**Validate**

- ``name`` is a string with sane min and max length.
- ``food_list_ingredients`` is an array with at least one item *if* ``food_list_meals`` is empty (and e.g. fewer than 1000 items)
- ``food_list_ingredients.*.id`` is a required integer 
- ``food_list_ingredients.*.ingredient_id`` is a required integer present in ``ingredients,id``
- ``food_list_ingredients.*.amount`` is a positive float
- ``food_list_ingredients.*.unit_id`` i a required integer present in ``units,id``
- ``food_list_meals`` is an array with at least one item *if* ``food_list_ingredients`` is empty (and e.g. fewer than 1000 items)
- ``food_list_meals.*.id`` is a required integer
- ``food_list_meals.*.meal_id`` is a required integer present in ``meals,id``
- ``food_list_meals.*.amount`` is a positive float
- ``food_list_meals.*.unit_id`` i a required integer present in ``units,id``

**Create**

- a ``food_list`` record with

  - given ``name``
  - ``mass_in_grams`` initialized to zero
  - ``user_id`` of user who made create request

- a ``food_list_ingredient`` or ``food_list_meal`` record for each respective element in supplied ``food_list_ingredients`` and ``food_list_meals``.

- **Ingredients:** For each ``food_list_ingredients`` element create a ``food_list_ingredient`` record with

  - ``food_list_id`` of ``food_list`` record
  - supplied ``ingredient_id``
  - supplied ``amount``
  - supplied ``unit_id``
  - ``mass_in_grams`` computed from supplied ``amount``, ``unit_id``, and ``ingredient_id``
  - increment running sum tracking food list's ``mass_in_grams``

- **Meals:** For each ``food_list_meals`` element create a ``food_list_meal`` record with

  - ``food_list_id`` of ``food_list`` record
  - supplied ``meal_id``
  - supplied ``amount``
  - supplied ``unit_id``
  - ``mass_in_grams`` computed from supplied ``amount``, ``unit_id``
  - increment running sum tracking food list's ``mass_in_grams``

**Update**

- Update ``name`` of existing ``food_list`` record
- Temporarily reset food list's ``mass_in_grams`` to zero

- **Ingredients:** delete/create/update protocol using existing ``foodList->food_list_ingredients`` in database and supplied ``food_list_ingredients`` array.

- **Meals:** delete/create/update protocol using existing ``foodList->food_list_meals`` in database and supplied ``food_list_meals`` array.

.. _validation-crud-rdi-profile:

Create or Update RDI profile
----------------------------

Incoming request looks like

.. code-block:: json
  
  {
    "name": "Foo",
    "rdi_profile_nutrients": [
      {
        "id": 0,
        "nutrient_id": 0,
        "rdi": 0.0
      }
    ]
  }

**Validate**

- ``name`` is a string with sane min and max length.
- ``rdi_profile_nutrients`` is a required array with exactly one entry for every Nutrient (distinct keys and length equal to ``Nutrients::count()``)  
- ``rdi_profile_nutrients.*.id`` is a required integer
- ``rdi_profile_nutrients.*.nutrient_id`` is a required integer present in ``nutrients,id`` with distinct values for every entry in ``rdi_profile_nutrients``
- ``rdi_profile_nutrients.*.rdi`` is a required positive float

**Create**

- ``rdi_profile`` record with supplied ``name`` and ``user_id`` of user who made create request
- For each entry in ``rdi_profile_nutrients``, create ``rdi_profile_nutrient`` record with

  - ``rdi_profile_id`` of ``rdi_profile`` record
  - supplied ``nutrient_id`` value
  - supplied ``rdi`` value

**Update**

- ``rdi_profile`` record with supplied ``name``
- For each entry in ``rdi_profile_nutrients``, look up corresponding ``rdi_profile_nutrient`` record based on ``rdi_profile_nutrients.*.id``, then update ``rdi`` with supplied ``rdi``.

Computing mass
--------------

Computing mass in grams for ingredients
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Input: ``ingredient_id``, ``ammount``, ``unit_id``

- If supplied ``unit_id`` is a unit of volume and supplied ``ingredient_id`` does not have a ``density_g_per_ml`` column, fail validation.
- If supplied ``unit_id`` is a unit of mass, multiply supplied ``amount`` by ``amount_in_grams`` column of ``to_grams`` table record for which ``foreign_unit_id`` equals supplied ``unit_id``
- If supplied ``unit_id`` is a unit of volume, multiply supplied ``amount`` by ``amount_in_milliliters`` column of ``to_milliliters`` table record for which ``foreign_unit_id`` equals supplied ``unit_id``.
  Then multiply result by ``density_g_per_ml`` value for supplied ``ingredient_id``.

Computing mass in grams for meals
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Input: ``ammount``, ``unit_id``

- If supplied ``unit_id`` is not a unit of mass, fail validation
- Multiply supplied ``amount`` by ``amount_in_grams`` column of ``to_grams`` table record for which ``foreign_unit_id`` equals supplied ``unit_id``
