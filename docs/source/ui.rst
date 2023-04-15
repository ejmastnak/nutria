User Interface
==============

Home
----

**Links to**

- Ingredients Index
- Meals Index
- Food List Index
- RDI Profile Index

**Actions**

Quick lookup of Nutrient profile of ingredient, meal, or food list

Ingredient CRUD
---------------

Index
^^^^^

Purpose: display an overview of all ingredients as an intermediate step to navigating to a specific ingredient.

**Props**

.. code-block:: json

  {
    "ingredients": [
      {
        "id": 0,
        "name": "Foo",
        "ingredient_category_id": 0
      }
    ],
    "ingredient_categories": [
      {
        "id": 0,
        "name": "Foo"
      }
    ]
  }

**UI**

Table with columns for:

- Ingredient name (links to Show)
- Ingredient category
- Pencil icon (links to Edit)
- Trash icon (links to Destroy)

**Filters**

- By ingredient name (fuzzy search filter)
- By ingredient category (select)

Show
^^^^

Purpose: display an ingredient's name, properties, and nutrient profile.

**Props**

.. code-block:: json

  {
    "ingredient": {
      "id": 0,
      "name": "Foo",
      "ingredient_category_id": 0,
      "ingredient_category": {
        "id": 0,
        "name": "Bar"
      },
      "density_g_per_ml": 0
    },
    "nutrient_profile": [
      {
        "nutrient": "Baz",
        "amount": 42,
        "unit": "g",
        "pdv": 100
      }
    ]
  }

**UI:** Standard RDI profile table.

**Links to:**

- Ingredients Home
- Edit
- Destroy

Create
^^^^^^

Purpose: create a new Ingredient.

**Props:**

All we strictly need is ``nutrient_id``, ``nutrient.display_name``, and ``unit.name``, but I'm preserving the same structure used for Ingredients/Edit in the hope of creating a reusable prop.

.. code-block:: json

  {
    "ingredient_nutrients": [
      {
        "id": 0,
        "nutrient_id": 0,
        "amount_per_100g": 0.0,
        "nutrient": {
          "id": 0,
          "display_name": "Baz",
          "unit_id": 0,
          "unit": {
            "id": 0,
            "name": "Bop"
          }
        }
      }
    ]
  }

**Form:** See :ref:`Validation: Create an Ingredient <validation-create-ingredient>`

**UI:** IngredientNutrient in table with columns:

- Nutrient name (static label)
- Nutrient amount (text input), prefilled either to zero or value of cloned ingredient.
- Nutrient unit (static text)

**Actions:**

- "Clone from existing ingredient" button
- Cancel (back)
- Save (redirects to Show)

Edit
^^^^

**Props:**

.. code-block:: json

  {
    "ingredient": {
      "id": 0,
      "name": "Foo",
      "ingredient_category_id": 0,
      "ingredient_category": {
        "ingredient_category_id": 0,
        "name": "Bar"
      },
      "density_g_per_ml": 0.0,
      "ingredient_nutrients": [
        {
          "id": 0,
          "ingredient_id": 0,
          "nutrient_id": 0,
          "amount_per_100g": 0.0,
          "nutrient": {
            "id": 0,
            "display_name": "Baz",
            "unit_id": 0,
            "unit": {
              "id": 0,
              "name": "Bop"
            }
          }
        }
      ]
    }
  }

**Form:** See :ref:`Validation: Update an Ingredient <validation-update-ingredient>`

**UI:** IngredientNutrients in table with columns:

- Nutrient name (static label)
- Nutrient amount (text input), prefilled with current value in ``ingredient.ingredient_nutrients.amount_per_100g``
- Nutrient unit (static text)

**Actions**

- Save
- Delete
- Cancel

Meal CRUD
---------

Index
^^^^^

Purpose: display an overview of all meals as an intermediate step to navigating to a specific meal.

**Props**

.. code-block:: json

  {
    "meals": [
      {
        "id": 0,
        "name": "Foo",
      }
    ]
  }

**UI**

Table with columns for:

- Meal name (links to Show)
- Pencil icon (links to Edit)
- Trash icon (links to Destroy)

Filter by meal name (fuzzy search filter)

Show
^^^^

Purpose: display a meals's name, constituent MealIngredients, and nutrient profile.

**Props**

.. code-block:: json

  {
    "meal": {
      "id": 0,
      "name": "Foo",
      "meal_ingredients": [
        {
          "meal_id": 0,
          "ingredient_id": 0,
          "amount": 0.0,
          "unit_id": 0,
          "ingredient": {
            "id": 0,
            "name": "Bar"
          },
          "unit": {
            "id": 0,
            "name": "Baz"
          }
        }
      ]
    },
    "nutrient_profile": [
      {
        "nutrient": "Bop",
        "amount": 0.0,
        "unit": "Blop",
        "pdv": 0.0
      }
    ]
  }

**Links to:**

- Meals Home
- Edit
- Destroy

**UI:** MealIngredients table in columns for:

- Ingredient name
- Amount (in originally specified units)
- Unit name

Nutrient Profile table.

Create
^^^^^^

Purpose: create a new Meal

**Props:** You need ``ingredients`` to use as MealIngredients and ``units`` to specify amount of each MealIngredient.

.. code-block:: json

  {
    "ingredients": [
      {
        "id": 0,
        "name": "Foo"
      }
    ],
    "units": [
      {
        "id": 0,
        "name": "Bar",
        "is_mass": true,
        "is_volume": false
      }
    ]
  }

**Form:** See :ref:`Validation: Create or Update a Meal <validation-crud-meal>`

**UI:** MealIngredients in table with columns:

- Ingredient name (combobox)
- Ingredient mass (text input for number)
- Unit (select)

**Actions:**

- Clone from existing meal
- Save button
- Cancel button (back)

Edit
^^^^

**Props:**

.. code-block:: json

  {
    "meal": {
      "id": 0,
      "name": "Foo",
      "meal_ingredients": [
        {
          "meal_id": 0,
          "ingredient_id": 0,
          "amount": 0.0,
          "unit_id": 0,
          "ingredient": {
            "id": 0,
            "name": "Bar",
            "density_g_per_ml": 0.0
          },
          "unit": {
            "id": 0,
            "name": "Baz"
          }
        }
      ]
    },
    "ingredients": [
      {
        "id": 0,
        "name": "Bop"
      }
    ],
    "units": [
      {
        "id": 0,
        "name": "Blap",
        "is_mass": true,
        "is_volume": false
      }
    ]
  }

**Form:** See :ref:`Validation: Create or Update a Meal <validation-crud-meal>`

**UI:** MealIngredients in table with columns:

- Ingredient name (combobox)
- Ingredient mass (text input for number)
- Unit (select)

**Actions:**

- Delete
- Save
- Cancel

Food List CRUD
--------------

Index
^^^^^

Purpose: display an overview of all food lists as an intermediate step to navigating to a specific food list.

**Props**

.. code-block:: json

  {
    "food_lists": [
      {
        "id": 0,
        "name": "Foo"
      }
    ]
  }

**UI**

Table with columns for:

- Food list name (links to Show)
- Pencil icon (links to Edit)
- Trash icon (links to Destroy)

Filter by food list name (fuzzy search filter)

Show
^^^^

**Props:**

.. code-block:: json

  {
    "food_list": {
      "id": 0,
      "name": "Foo",
      "food_list_ingredients": [
        {
          "id": 0,
          "food_list_id": 0,
          "ingredient_id": 0,
          "amount": 0.0,
          "unit_id": 0,
          "ingredient": {
            "id": 0,
            "name": "Bar"
          },
          "unit": {
            "id": 0,
            "name": "Baz"
          }
        }
      ],
      "food_list_meals": [
        {
          "id": 0,
          "food_list_id": 0,
          "meal_id": 0,
          "amount": 0.0,
          "unit_id": 0,
          "meal": {
            "id": 0,
            "name": "Bar"
          },
          "unit": {
            "id": 0,
            "name": "Baz"
          }
        }
      ]
    },
    "nutrient_profile": [
      {
        "nutrient": "Bop",
        "amount": 0.0,
        "unit": "g",
        "pdv": 0.0
      }
    ]
  }

**UI:**

FoodListIngredients (if present) in table with columns:

- Ingredient name
- Amount
- Unit

FoodListMeals (if present) in table with columns:

- Meal name
- Amount
- Unit

Nutrient profile table.

**Actions:**

- Edit
- Delete
- Back (e.g. to ingredients home)

Create
^^^^^^

Purpose: create a new Food List

**Props:** You need ``ingredients`` and ``meals`` to use as FoodListIngredients and FoodListMeals and ``units`` to specify amount of each ingredient/meal.

.. code-block:: json

  {
    "ingredients": [
      {
        "id": 0,
        "name": "Foo"
      }
    ],
    "meals": [
      {
        "id": 0,
        "name": "Bar"
      }
    ],
    "units": [
      {
        "id": 0,
        "name": "Baz",
        "is_mass": true,
        "is_volume": false
      }
    ]
  }

**Form:** See :ref:`Validation: Create or Update Food List <validation-crud-food-list>`

**UI:** 

FoodListIngredients in table with columns:

- Ingredient name (combobox with search over ingredients)
- Ingredient mass (text input for number)
- Unit (select over units)

FoodListMeals in table with columns:

- Meal name (combobox with search over meals)
- Meal mass (text input for number)
- Unit (select over units)

**Actions:**

- Clone from existing Food List
- Save button
- Cancel button (back)

Edit
^^^^

Purpose: update an existing new Food List

**Props:** In addition to the Food List itself, you need ``ingredients`` and ``meals`` to use as FoodListIngredients and FoodListMeals and ``units`` to specify amount of each ingredient/meal.

.. code-block:: json

  {
    "food_list": {
      "id": 0,
      "name": "Foo",
      "food_list_ingredients": [
        {
          "id": 0,
          "food_list_id": 0,
          "ingredient_id": 0,
          "amount": 0.0,
          "unit_id": 0,
          "ingredient": {
            "id": 0,
            "name": "Bar"
          },
          "unit": {
            "id": 0,
            "name": "Baz"
          }
        }
      ],
      "food_list_meals": [
        {
          "id": 0,
          "food_list_id": 0,
          "meal_id": 0,
          "amount": 0.0,
          "unit_id": 0,
          "meal": {
            "id": 0,
            "name": "Bar"
          },
          "unit": {
            "id": 0,
            "name": "Baz"
          }
        }
      ]
    },
    "ingredients": [
      {
        "id": 0,
        "name": "Foo"
      }
    ],
    "meals": [
      {
        "id": 0,
        "name": "Bar"
      }
    ],
    "units": [
      {
        "id": 0,
        "name": "Baz",
        "is_mass": true,
        "is_volume": false
      }
    ]
  }

**Form:** See :ref:`Validation: Create or Update Food List <validation-crud-food-list>`

**UI:** 

FoodListIngredients in table with columns:

- Ingredient name (combobox with search over ingredients)
- Ingredient mass (text input for number)
- Unit (select over units)

FoodListMeals in table with columns:

- Meal name (combobox with search over meals)
- Meal mass (text input for number)
- Unit (select over units)

**Actions:**

- Delete
- Save
- Cancel

RDI Profile CRUD
----------------

Index
^^^^^

Purpose: display an overview of all RDI profiles as an intermediate step to navigating to a specific profile.

**Props**

.. code-block:: json

  {
    "rdi_profiles": [
      {
        "id": 0,
        "name": "Foo"
      }
    ]
  }

**UI**

Table with columns for:

- RDI Profile name (links to Show)
- Pencil icon (links to Edit)
- Trash icon (links to Destroy)

Filter by RDI profile name (fuzzy search filter)

Show
^^^^

Purpose: display the RDI value for every nutrient in an RDI profile.

**Props**

.. code-block:: json

  {
    "rdi_profile": {
      "id": 0,
      "name": "Foo"
    },
    "rdi_profile_nutrients": [
      {
        "id": 0,
        "rdi_profile_id": 0,
        "nutrient_id": 0,
        "rdi": 0.0,
        "nutrient": {
          "id": 0,
          "display_name": "Bar",
          "unit_id": 0,
          "unit": {
            "id": 0,
            "name": "Baz"
          }
        }
      }
    ]
  }

**UI:** RdiProfileNutrients in table with columns:

- Nutrient name
- RDI value
- Unit (in nutrient's preferred units)

It might also be interesting to display RDI value relative to FDA-recommended RDI value. In this case you'd need to send (some information about) the FDA RDI profile as a prop.

**Links to:**

- RDI Profile Home
- Edit
- Destroy

Create
^^^^^^

Purpose: create a new RDI Profile.

**Props:** All we strictly need is ``nutrient_id``, ``nutrient.display_name``, and ``unit.name``, but I'm preserving the same structure used for RdiProfiles/Edit in the hope of creating a reusable prop.

.. code-block:: json

  {
    "rdi_profile_nutrients": [
      {
        "id": 0,
        "rdi_profile_id": 0,
        "nutrient_id": 0,
        "rdi": 0.0,
        "nutrient": {
          "id": 0,
          "display_name": "Foo",
          "unit_id": 0,
          "unit": {
            "id": 0,
            "name": "Bar"
          }
        }
      }
    ]
  }

**Form:** See :ref:`Validation: Create an RDI Profile <validation-create-rdi-profile>`

**UI:** RdiProfileNutrients in table with columns:

- Nutrient name (static label)
- Nutrient amount (text input), prefilled either to zero or value from cloned RDI Profile.
- Nutrient unit (static text)

**Actions:**

- "Clone from existing RDI Profile" button
- Cancel
- Save

Edit
^^^^

Purpose: update an existing RDI Profile.

**Props:** 

.. code-block:: json

  {
    "rdi_profile": {
      "id": 0,
      "name": "Foo"
    },
    "rdi_profile_nutrients": [
      {
        "id": 0,
        "rdi_profile_id": 0,
        "nutrient_id": 0,
        "rdi": 0.0,
        "nutrient": {
          "id": 0,
          "display_name": "Bar",
          "unit_id": 0,
          "unit": {
            "id": 0,
            "name": "Baz"
          }
        }
      }
    ]
  }

**Form:** See :ref:`Validation: Update an RDI Profile <validation-update-rdi-profile>`

**UI:** RdiProfileNutrients in table with columns:

- Nutrient name (static label)
- Nutrient amount (text input), prefilled either to current value
- Nutrient unit (static text)

**Actions:**

- Save
- Delete
- Cancel
