Validation
==========

CRUD
----

Ingredient
^^^^^^^^^^

Incoming request looks like

.. code-block:: json

  {
    "name": "Foo",
    "category_id": null,
    "density_g_per_ml": null,
    "nutrients": [
      {
        "nutrient_id": 0,
        "amount_per_100g": 0.0
      }
    ]
  }

**Validate**

- ``name`` is a string with sane min and max length.
- ``category_id``, is either null or present in ``ingredient_categories,id``
- ``density_g_per_ml`` is either null or a positive float 
- ``nutrients`` is an array and contains exactly one item for each record in ``nutrients`` table
- ``nutrients.*.nutrient_id`` is present in ``nutrients,id``
- ``nutrients.*.amount_per_100g`` is a positive float

**Create**

- an ``ingredient`` record with null ``fdc_id`` and given ``name``, ``ingredient_category_id``, and ``density_g_per_ml``.
- a ``ingredient_nutrient`` record for each element in supplied ``nutrients`` with:

  - ``ingredient_id`` of ``ingredient`` record
  - given ``nutrient_id`` 
  - ``amount_per_100g``

Meal
^^^^

Incoming request looks like

.. code-block:: json
    
  {
    "name": "Foo",
    "ingredients": [
      {
        "ingredient_id": 0,
        "amount": 0.0,
        "unit_id": 0
      }
    ]
  }

**Validate**

- ``name`` is a string with sane min and max length.
- ``ingredients`` is an array with at least one item (and e.g. less than 1000 items)
- ``ingredients.*.ingredient_id`` is present in ``ingredients,id``
- ``ingredients.*.amount`` is a positive float
- ``ingredients.*.unit_id`` is present in ``units,id``

**Create**

- a ``meal`` record with given ``name``
- a ``meal_ingredient`` record for each element in supplied ``ingredients`` with:

  - ``meal_id`` of ``meal`` record
  - supplied ``ingredient_id``
  - supplied ``amount``
  - supplied ``unit_id``
  - ``mass_in_grams`` computed from supplied ``amount``, ``unit_id``, and ``ingredient_id``

Food list
^^^^^^^^^

Incoming request looks like

.. code-block:: json
  
  {
    "name": "Foo",
    "food_list_items": [
      {
        "ingredient_id": 0,
        "meal_id": 0,
        "amount": 0.0,
        "unit_id": 0
      }
    ]
  }

**Validate**

- ``name`` is a string with sane min and max length.
- ``food_list_items`` is an array with at least one item (and e.g. less than 1000 items)
- ``food_list_items.*.ingredient_id`` is either null or present in ``ingredients,id``
- ``food_list_items.*.meal_id`` is either null or present in ``meals,id``
- ``food_list_items.*.amount`` is a positive float
- ``food_list_items.*.unit_id`` is present in ``units,id``

**Create**

- a ``food_list`` record with given ``name``
- a ``food_list_ingredient`` or ``food_list_meal`` record for each element in supplied ``food_list_items``

Processing ``food_list_items``:

- **Ingredients:** If ``meal_id`` is null and ``ingredient_id`` exists in ``ingredients,id``, create ``food_list_ingredient`` record with

  - ``food_list_id`` of ``food_list`` record
  - supplied ``ingredient_id``
  - supplied ``amount``
  - supplied ``unit_id``
  - ``mass_in_grams`` computed from supplied ``amount``, ``unit_id``, and ``ingredient_id``

- **Meals:** If ``ingredient_id`` is null and ``meal_id`` exists in ``meals,id``, create ``food_list_meal`` record with:

  - ``food_list_id`` of ``food_list`` record
  - supplied ``meal_id``
  - supplied ``amount``
  - supplied ``unit_id``
  - ``mass_in_grams`` computed from supplied ``amount``, ``unit_id``

- **Otherwise:** fail validation

RDI profile
^^^^^^^^^^^

Incoming request looks like

.. code-block:: json
  
  {
    "name": "Foo",
    "nutrients": [
      {
        "nutrient_id": 0,
        "rdi": 0.0
      }
    ]
  }

**Validate**

- ``name`` is a string with sane min and max length.
- ``nutrients`` is an array and contains exactly one item for each record in ``nutrients`` table
- ``nutrients.*.nutrient_id`` is present in ``nutrients,id``
- ``nutrients.*.rdi`` is a positive float

**Create**

- ``rdi_profile`` record with supplied ``name``
- For entry in ``nutrients``, create ``rdi_profile_nutrient`` record with

  - ``rdi_profile_id`` of ``rdi_profile`` record
  - supplied ``nutrient_id`` value (validate that ``nutrient_id`` exists in ``nutrients,id``)
  - supplied ``rdi`` value (should be a positive float)

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

View nutrient profiles
----------------------

Ingredient nutrient profile
^^^^^^^^^^^^^^^^^^^^^^^^^^^

Incoming request looks like

.. code-block:: json
  
  {
    "ingredient_id": 0,
    "amount": 0.0,
    "unit_id": 0,
    "rdi_profile_id": 0
  }

**Validate**

- ``ingredient_id`` exists in ``ingredients,id``
- ``amount`` is a positive float
- ``unit_id`` exists in ``units,id`` and is either a mass or volume
- ``rdi_profile_id`` exists in ``rdi_profiles,id``

Meal nutrient profile
^^^^^^^^^^^^^^^^^^^^^

Incoming request looks like

.. code-block:: json
  
  {
    "meal_id": 0,
    "amount": 0.0,
    "unit_id": 0,
    "rdi_profile_id": 0
  }

**Validate**

- ``meal_id`` exists in ``meals,id``
- ``amount`` is a positive float
- ``unit_id`` exists in ``units,id`` and is a mass
- ``rdi_profile_id`` exists in ``rdi_profiles,id``

Food list nutrient profile
^^^^^^^^^^^^^^^^^^^^^^^^^^

Incoming request looks like

.. code-block:: json
  
  {
    "food_list_id": 0,
    "rdi_profile_id": 0
  }

**Validate**

- ``food_list_id`` exists in ``food_lists,id``
- ``rdi_profile_id`` exists in ``rdi_profiles,id``
