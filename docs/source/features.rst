Features
========

.. _feature-create-ingredient:

Create an ingredient
--------------------

User must specify:

- Ingredient name
- Amount of each nutrient, in nutrient's preferred units, per 100 grams of ingredient
- Ingredient category (may be null)
- Density (for volume-mass conversion; may be null)

UI: table of all nutrients with three columns:

#. Nutrient name (static text)
#. Amount (text input)
#. Unit (static text)

Every amount field is initialized to zero, so user has the option to only specify a few nutrients of interest and leave the other nutrients blank.

A request would look something like

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

.. _feature-create-meal:

Create a meal
-------------

User must specify:

- Meal name
- Ingredients in meal
- How much of each ingredient

Request looks like:

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

.. _feature-create-food-list:

Create a food list
------------------

User must specify:

- Which foods
- How much of each food.

UI: Use a buffer array of ``food_list_items``.
Support deleting items from buffer and changing amount and unit, but not name.

Have one small form with fuzzy search text input, amount text input, and unit select menu that adds items to ``food_list_items``.

Structure of a ``food_list_item``:

.. code-block:: javascript
  
  {
    // One of ``ingredient_id`` or ``meal_id`` is null, which tells you if the
    // item is an ingredient or meal.
    "id": 0,
    "ingredient_id": 0,
    "meal_id": 0,
    "amount": 0.0,
    "unit_id": 0
  }

Request send to backend would be the array of ``food_list_items``.

.. code-block:: javascript
  
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

.. _feature-create-rdi-profile:

Create an RDI profile
---------------------

User must specify:

- RDI profile name
- Which nutrients
- RDI for each nutrient
  (i.e. how much of each nutrient should be consumed in a day, in nutrient's preferred units)

UI: table of all nutrients with three columns:

#. Nutrient name (static text)
#. RDI (text input)
#. Unit (static text)

Every RDI field is initialized to nutrient's standard RDI value, so user has the option to only specify a few nutrients of interest and leave the other nutrients with default values.

A request would look something like

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

View ingredient nutrient profile
--------------------------------

User must specify:

- Which ingredient
- How much (amount, unit)
- RDI profile to use for computing PDV

Have 100 g preselected as default amount, but allow customization.
Have standard RDI profile preselected, but allow customization.

Request send to backend would look like

.. code-block:: json
  
  {
    "ingredient_id": 0,
    "amount": 0.0,
    "unit_id": 0,
    "rdi_profile_id": 0
  }

Response returned to frontend would look like

.. code-block:: json
  
  {
    "food_list": [
      {
        "ingredient": {
          "id": 0,
          "name": "Foo"
        },
        "meal": null,
        "amount": 0.0,
        "unit": "g"
      }
    ],
    "nutrient_profile": [
      {
        "nutrient": "Protein",
        "amount": 25.0,
        "unit": "g",
        "pdv": "50"
      }
    ]
  }

View meal nutrient profile
--------------------------

User must specify:

- Which meal
- How much (mass, unit)
- RDI profile to use for computing PDV

Have default meal mass preselected as default amount, but allow customization.
Have standard RDI profile preselected, but allow customization.

Request send to backend would look like

.. code-block:: json
  
  {
    "meal_id": 0,
    "amount": 0.0,
    "unit_id": 0,
    "rdi_profile_id": 0
  }

Response returned to frontend would look like

.. code-block:: json
  
  {
    "food_list": [
      {
        "ingredient": null,
        "meal": {
          "id": 0,
          "name": "Foo"
        },
        "amount": 0.0,
        "unit": "g"
      }
    ],
    "nutrient_profile": [
      {
        "nutrient": "Protein",
        "amount": 25.0,
        "unit": "g",
        "pdv": "50"
      }
    ]
  }

View food list nutrient profile
-------------------------------

User must specify:

- Which food list
- RDI profile to use for computing PDV

Have standard RDI profile preselected, but allow customization.

Request send to backend would look like

.. code-block:: json
  
  {
    "food_list_id": 0,
    "rdi_profile_id": 0
  }

Response returned to frontend would look like

.. code-block:: json
  
  {
    "food_list": [
      {
        "ingredient": null,
        "meal": {
          "id": 0,
          "name": "Foo"
        },
        "amount": 0.0,
        "unit": "g"
      }
    ],
    "nutrient_profile": [
      {
        "nutrient": "Protein",
        "amount": 25.0,
        "unit": "g",
        "pdv": "50"
      }
    ]
  }
