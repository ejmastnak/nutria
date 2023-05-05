User Interface
==============

Home
----

**Links to**

- Ingredients Index
- Meals Index
- Food List Index
- Intake Guideline Index

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
    "user_ingredients": [
      {
        "id": 0,
        "name": "Bar",
        "ingredient_category_id": 0
      }
    ],
    "ingredient_categories": [
      {
        "id": 0,
        "name": "Baz"
      }
    ],
    "can_create": false
  }

- ``ingredients`` for search over FDA ingredients, which the user cannot edit
- ``user_ingredients`` for listing of user-created ingredients
- ``ingredient_categories`` for filtering ingredients by category
- ``can_create`` to control display of "Create ingredient" and "Clone ingredient" button

**UI**

FDA Ingredients available via fuzzy search with N ~ 10 results---there are too many to display in a table.

User-created Ingredients in table with columns for:

- Ingredient name (links to Show)
- Ingredient category
- Pencil (Edit) and Trash can (Delete) icons

**Filters**

- By ingredient name (fuzzy search filter)
- By ingredient category (select)

**Links to:** 

- Create new ingredient
- Clone existing ingredient

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
      "density_g_per_ml": 0.0
    },
    "nutrient_profiles": [
      {
        "intake_guideline_id": 0,
        "nutrient_profile": [
          {
            "nutrient": "Baz",
            "nutrient_category_id": 0,
            "amount": 42,
            "precision": 0,
            "unit": "g",
            "pdv": 100
          }
        ]
      }
    ],
    "intake_guidelines": [
      {
        "id": 0,
        "name": "Boop"
      }
    ],
    "ingredients": [
      {
        "id": 0,
        "name": "Blap",
        "ingredient_category_id": 0
      }
    ],
    "nutrient_categories": [
      {
        "id": 0,
        "name": "Blop"
      }
    ],
    "can_edit": false,
    "can_clone": false,
    "can_delete": false,
    "can_create": false
  }

- ``ingredient`` to display ingredient info
- ``nutrient_profiles`` to display ingredient's nutrient profile(s)
- ``intake_guidelines`` to show nutrient profiles for different intake guidelines
- ``ingredients`` for "Search for another ingredient" without having to go back to Ingredients/Index
- ``nutrient_categories`` only to pass to NutrientProfile component to split up nutrient profile into vitamins, minerals, macronutrients.
- Auth props to conditionally display Edit, Clone, Delete, and Create buttons.

**UI:** Standard intake guideline table.

**Links to:**

- Edit
- Clone
- Delete
- Create
- Search
- Index

Create
^^^^^^

Purpose: create a new Ingredient.

**Props:**

.. code-block:: json

  {
    "ingredient": {
      "id": 0,
      "name": "",
      "ingredient_category_id": 0,
      "density_g_per_ml": null,
      "ingredient_nutrients": [
        {
          "id": null,
          "nutrient_id": 0,
          "amount_per_100g": 0.0,
          "nutrient": {
            "id": 0,
            "display_name": "Foo",
            "unit_id": 0,
            "nutrient_category_id": 0,
            "precision": 0,
            "display_order_id": 0,
            "unit": {
              "id": 0,
              "name": "Bar"
            }
          }
        }
      ]
    },
    "ingredients": [
      {
        "id": 0,
        "name": "Bop",
        "ingredient_category_id": 0
      }
    ],
    "ingredient_categories": [
      {
        "id": 0,
        "name": "Baz"
      }
    ],
    "nutrient_categories": [
      {
        "id": 0,
        "name": "Blop"
      }
    ],
    "clone": false,
    "can_view": false,
    "can_create": false
  }

- ``ingredient`` is used by Edit and Clone, which share a CreateOrEdit component with Create.
  Although Create strictly needs only ``nutrient_id``, ``nutrient.display_name``, and ``unit.name``, I'm preserving the ``ingredient`` prop structure to be able to use the same CreateOrEdit component for Create.
- ``ingredients`` for "Search for another ingredient" and "Clone existing ingredient"
- ``ingredient_categories`` to allow user to choose the ingredient's category.
- ``nutrient_category`` to split up IngredientNutrients into vitamins, minerals, and macronutrients.
- ``clone`` to conditionally display "Cloned from Foo" message
- Auth props to conditionally display View (for clone pages) and Clone/Create buttons

**Form:** See :ref:`Validation: Create or Update an Ingredient <validation-crud-ingredient>`

**UI:** IngredientNutrient in table with columns:

- Nutrient name (static label)
- Nutrient amount (text input), prefilled either to zero or value of cloned ingredient.
- Nutrient unit (static text)

**Links to:**

- View original (for Clone)
- Create blank (for Clone)
- Clone existing (for Create)
- Search
- Index

**Actions:**

- Store
- Cancel

Edit
^^^^

**Props:**

.. code-block:: json

  {
    "ingredient": {
      "id": 0,
      "name": "Foo",
      "ingredient_category_id": 0,
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
            "nutrient_category_id": 0,
            "precision": 0,
            "display_order_id": 0,
            "unit": {
              "id": 0,
              "name": "Bop"
            }
          }
        }
      ]
    },
    "ingredients": [
      {
        "id": 0,
        "name": "Boop",
        "ingredient_category_id": 0
      }
    ],
    "ingredient_categories": [
      {
        "id": 0,
        "name": "Blap"
      }
    ],
    "nutrient_categories": [
      {
        "id": 0,
        "name": "Boop"
      }
    ],
    "can_view": false,
    "can_clone": false,
    "can_delete": false,
    "can_create": false
  }

- ``ingredient`` for current ingredient information
- ``ingredients`` for "Search for another ingredient" and "Clone existing ingredient"
- ``ingredient_categories`` to allow user to choose the ingredient's category.
- ``nutrient_category`` to split up IngredientNutrients into vitamins, minerals, and macronutrients.
- Auth props to conditionally display View, Clone, Delete, Create buttons.

**Form:** See :ref:`Validation: Create or Update an Ingredient <validation-crud-ingredient>`

**UI:** IngredientNutrients in table with columns:

- Nutrient name (static label)
- Nutrient amount (text input), prefilled with current value in ``ingredient.ingredient_nutrients.amount_per_100g``
- Nutrient unit (static text)

**Links to:**

- View
- Clone
- Delete
- Create
- Search
- Index

**Actions:**

- Store
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
    ],
    "can_create": false
  }

- ``meals`` for search over meals
- ``can_create`` to control display of "Create meal" and "Clone meal" button

**UI**

Table with columns for:

- Meal name (links to Show)
- Pencil icon (links to Edit)
- Trash icon (links to Destroy)

Filter by meal name (fuzzy search filter)

**Links to:**

- Create new ingredient
- Clone existing ingredient

Show
^^^^

Purpose: display a meals's name, constituent MealIngredients, and nutrient profile.

**Props**

.. code-block:: json

  {
    "meal": {
      "id": 0,
      "name": "Foo",
      "mass_in_grams": 0.0,
      "meal_ingredients": [
        {
          "id": 0,
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
    "nutrient_profiles": [
      {
        "intake_guideline_id": 0,
        "nutrient_profile": [
          {
            "nutrient": "Blah",
            "nutrient_category_id": 0,
            "amount": 42,
            "precision": 0,
            "unit": "g",
            "pdv": 100
          }
        ]
      }
    ],
    "intake_guidelines": [
      {
        "id": 0,
        "name": "Bap"
      }
    ],
    "meals": [
      {
        "id": 0,
        "name": "Boop"
      }
    ],
    "nutrient_categories": [
      {
        "id": 0,
        "name": "Blap"
      }
    ],
    "can_edit": false,
    "can_clone": false,
    "can_delete": false,
    "can_create": false
  }

- ``meal`` to display meal info
- ``nutrient_profiles`` to display meal's nutrient profile(s)
- ``intake_guidelines`` to show nutrient profiles for different intake guidelines
- ``meals`` for "Search for another meal"
- ``nutrient_categories`` to split up nutrient profile into vitamins, minerals, macronutrients
- Auth props to conditionally display Edit, Clone, Delete, and Create buttons.


**UI:** MealIngredients table with columns for:

- Ingredient name
- Amount (in originally specified units)
- Unit name

Nutrient Profile table.

**Links to:**

- Edit
- Clone
- Delete
- Create
- Search
- Index

Create
^^^^^^

Purpose: create a new Meal

**Props:**

.. code-block:: json

  {
    "meal": null,
    "meals": [
      {
        "id": 0,
        "name": "Boo"
      }
    ],
    "ingredients": [
      {
        "id": 0,
        "name": "Foo"
        "ingredient_category_id": 0,
        "density_g_per_ml": null
      }
    ],
    "ingredient_categories": [
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
    ],
    "clone": false,
    "can_view": false,
    "can_create": false
  }

- ``meal`` is not directly needed for Create, but is used for cloning meals.
- ``meals`` for "Search for another" and "Clone existing"
- ``ingredients`` (FDA *and* user ingredients) to use as MealIngredients.
  ``density_g_per_ml`` to determine if ingredient amount can be specified in volume units.
- ``ingredient_categories`` for filtering Ingredients when searching
- ``units`` to specify amount of each MealIngredient.
- ``clone`` to conditionally display "Cloned from Foo" message
- Auth props to conditionally display View (for clone pages) and Clone/Create buttons

**Form:** See :ref:`Validation: Create or Update a Meal <validation-crud-meal>`

**UI:** MealIngredients in table with columns:

- Ingredient name (combobox)
- Ingredient mass (text input for number)
- Unit (select)

**Links to:**

- View original (for Clone)
- Create blank (for Clone)
- Clone existing (for Create)
- Search
- Index

**Actions:**

- Store
- Cancel

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
          "id": 0,
          "meal_id": 0,
          "ingredient_id": 0,
          "amount": 0.0,
          "unit_id": 0,
          "ingredient": {
            "id": 0,
            "name": "Bar",
            "density_g_per_ml": null
          },
          "unit": {
            "id": 0,
            "name": "Baz"
          }
        }
      ]
    },
    "meals": [
      {
        "id": 0,
        "name": "Boo"
      }
    ],
    "ingredients": [
      {
        "id": 0,
        "name": "Bop",
        "ingredient_category_id": 0,
        "density_g_per_ml": null
      }
    ],
    "ingredient_categories": [
      {
        "id": 0,
        "name": "Blap"
      }
    ],
    "units": [
      {
        "id": 0,
        "name": "Boop",
        "is_mass": true,
        "is_volume": false
      }
    ],
    "can_view": false,
    "can_clone": false,
    "can_delete": false,
    "can_create": false
  }

- ``meal`` to display current meal information
- ``meals`` for "Search for another" and "Clone existing"
- ``ingredients`` (FDA *and* user ingredients) to use as MealIngredients.
  ``density_g_per_ml`` to determine if ingredient amount can be specified in volume units.
- ``ingredient_categories`` for filtering Ingredients when searching
- ``units`` to specify amount of each MealIngredient.
- Auth props to conditionally display View, Clone, Delete, Create buttons.


**Form:** See :ref:`Validation: Create or Update a Meal <validation-crud-meal>`

**UI:** MealIngredients in table with columns:

- Ingredient name (combobox)
- Ingredient mass (text input for number)
- Unit (select)

**Links to:**

- View
- Clone
- Delete
- Create
- Search
- Index

**Actions:**

- Store
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
    ],
    "can_create": false
  }

- ``food_lists`` to show overview of food lists
- ``can_create`` to control display of "Create food list" and "Clone existing food list"

**UI**

Table with columns for:

- Food list name (links to Show)
- Pencil icon (links to Edit)
- Trash icon (links to Destroy)

Filter by food list name (fuzzy search filter)

**Links to:**

- Create new
- Clone existing

Show
^^^^

**Props:**

.. code-block:: json

  {
    "food_list": {
      "id": 0,
      "name": "Foo",
      "mass_in_grams": 0.0,
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
    "nutrient_profiles": [
      {
        "intake_guideline_id": 0,
        "nutrient_profile": [
          {
            "nutrient": "Blah",
            "nutrient_category_id": 0,
            "amount": 42,
            "precision": 0,
            "unit": "g",
            "pdv": 100
          }
        ]
      }
    ],
    "intake_guidelines": [
      {
        "id": 0,
        "name": "Bap"
      }
    ],
    "food_lists": [
      {
        "id": 0,
        "name": "Boop"
      }
    ],
    "nutrient_categories": [
      {
        "id": 0,
        "name": "Blap"
      }
    ],
    "can_edit": false,
    "can_clone": false,
    "can_delete": false,
    "can_create": false
  }

- ``food_list`` to display food list info (``mass_in_grams`` to pass to NutrientProfile)
- ``nutrient_profiles`` to display food list's nutrient profile(s)
- ``intake_guidelines`` to show nutrient profiles for different intake guidelines
- ``food_lists`` to "Search for another food list"
- ``nutrient_categories`` to split up nutrient profile into vitamins, minerals, macronutrients
- Auth props to conditionally display Edit, Clone, Delete, and Create buttons.


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

**Links to:**

- Edit
- Clone
- Delete
- Create
- Search
- Index

Create
^^^^^^

Purpose: create a new Food List

.. code-block:: json

  {
    "food_list": null,
    "food_lists": [
      {
        "id": 0,
        "name": "Boo"
      }
    ],
    "ingredients": [
      {
        "id": 0,
        "name": "Foo",
        "ingredient_category_id": 0,
        "density_g_per_ml": null
      }
    ],
    "meals": [
      {
        "id": 0,
        "name": "Bar",
        "mass_in_grams": 0.0
      }
    ],
    "ingredient_categories": [
      {
        "id": 0,
        "name": "Baz"
      }
    ],
    "units": [
      {
        "id": 0,
        "name": "Bop",
        "is_mass": true,
        "is_volume": false
      }
    ],
    "clone": false,
    "can_view": false,
    "can_create": false
  }

- ``food_list`` is not directly needed for Create, but is used for cloning food lists.
- ``food_lists`` for "Search for another" and "Clone existing"
- ``ingredients`` (FDA *and* user ingredients) to use as FoodListIngredients.
  ``density_g_per_ml`` to determine if ingredient amount can be specified in volume units.
- ``meals`` to use as FoodListMeals (``mass_in_grams`` to use as default meal mass)
- ``ingredient_categories`` for filtering Ingredients when searching
- ``units`` to specify amount of each FoodListIngredient and FoodListMeal
- ``clone`` to conditionally display "Cloned from Foo" message
- Auth props to conditionally display View (for clone pages) and Clone/Create buttons

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

**Links to:**

- View original (for Clone)
- Create blank (for Clone)
- Clone existing (for Create)
- Search
- Index

**Actions:**

- Store
- Cancel

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
            "name": "Bar",
            "density_g_per_ml": null
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
    "food_lists": [
      {
        "id": 0,
        "name": "Boo"
      }
    ],
    "ingredients": [
      {
        "id": 0,
        "name": "Foo",
        "ingredient_category_id": 0,
        "density_g_per_ml": null
      }
    ],
    "meals": [
      {
        "id": 0,
        "name": "Bar",
        "mass_in_grams": 0.0
      }
    ],
    "ingredient_categories": [
      {
        "id": 0,
        "name": "Baz"
      }
    ],
    "units": [
      {
        "id": 0,
        "name": "Blap",
        "is_mass": true,
        "is_volume": false
      }
    ],
    "can_view": false,
    "can_clone": false,
    "can_delete": false,
    "can_create": false
  }

- ``food_list`` to display current food list information
- ``food_lists`` for "Search for another" and "Clone existing"
- ``ingredients`` (FDA *and* user ingredients) to use as FoodListIngredients
  ``density_g_per_ml`` to determine if ingredient amount can be specified in volume units.
- ``meals`` to use as FoodListMeals (``mass_in_grams`` to use as default meal mass)
- ``ingredient_categories`` for filtering Ingredients when searching
- ``units`` to specify amount of each FoodListIngredient and FoodListMeal
- Auth props to conditionally display View, Clone, Delete, Create buttons.

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

**Links to:**

- View
- Clone
- Delete
- Create
- Search
- Index

**Actions:**

- Store
- Cancel

Intake Guideline CRUD
---------------------

Index
^^^^^

Purpose: display an overview of all intake guidelines as an intermediate step to navigating to a specific profile.

**Props**

.. code-block:: json

  {
    "intake_guidelines": [
      {
        "id": 0,
        "name": "Foo",
        "can_edit": false,
        "can_delete": false
      }
    ],
    "can_create": false
  }

- ``intake_guidelines`` to display all intake guidelines.
  I'm merging built-in FDA intake guideline with user intake guidelines; ``can_edit`` and ``can_delete`` are to conditionally display edit/delete links for user profiles.
- ``can_create`` to conditionally display Create and Clone buttons.

**UI**

Table with columns for:

- Intake Guideline name (links to Show)
- Pencil icon (links to Edit)
- Trash icon (links to Destroy)

Filter by intake guideline name (fuzzy search filter)

**Links to:**

- Create new
- Clone existing

Show
^^^^

Purpose: display the recommended daily intake for every nutrient in an intake guideline.

**Props**

.. code-block:: json

  {
    "intake_guideline": {
      "id": 0,
      "name": "Foo",
      "intake_guideline_nutrients": [
        {
          "id": 0,
          "intake_guideline_id": 0,
          "nutrient_id": 0,
          "rdi": 0.0,
          "nutrient": {
            "id": 0,
            "display_name": "Bar",
            "unit_id": 0,
            "nutrient_category_id": 0,
            "precision": 0,
            "display_order_id": 0,
            "unit": {
              "id": 0,
              "name": "Baz"
            }
          }
        }
      ]
    },
    "intake_guidelines": [
      {
        "id": 0,
        "name": "Boop"
      }
    ],
    "nutrient_categories": [
      {
        "id": 0,
        "name": "Blap"
      }
    ],
    "can_edit": false,
    "can_clone": false,
    "can_delete": false,
    "can_create": false
  }

- ``intake_guideline`` to display intake guideline info.
  ``nutrient_category_id`` to split up intake guideline nutrients by category.
- ``intake_guidelines`` for "Search for another intake guideline"
- ``nutrient_categories`` to split up display of intake guideline into vitamins, minerals, macronutrients
- Auth props to conditionally display Edit, Clone, Delete, and Create buttons.


**UI:** IntakeGuidelineNutrients in table with columns:

- Nutrient name
- RDI value
- Unit (in nutrient's preferred units)

It might also be interesting to display RDI value relative to FDA-recommended RDI value. In this case you'd need to send (some information about) the FDA intake guideline as a prop.

**Links to:**

- Edit
- Clone
- Delete
- Create
- Search
- Index

Create
^^^^^^

Purpose: create a new Intake Guideline.

.. code-block:: json

  {
    "intake_guideline": {
      "id": 0,
      "name": "",
      "intake_guideline_nutrients": [
        {
          "id": 0,
          "intake_guideline_id": 0,
          "nutrient_id": 0,
          "rdi": 0.0,
          "nutrient": {
            "id": 0,
            "display_name": "Bar",
            "unit_id": 0,
            "nutrient_category_id": 0,
            "precision": 0,
            "display_order_id": 0,
            "unit": {
              "id": 0,
              "name": "Baz"
            }
          }
        }
      ]
    },
    "intake_guidelines": [
      {
        "id": 0,
        "name": "Boop"
      }
    ],
    "nutrient_categories": [
      {
        "id": 0,
        "name": "Blap"
      }
    ],
    "clone": false,
    "can_view": false,
    "can_create": false
  }

- ``intake_guideline`` is used by Edit and Clone, which share a CreateOrEdit component with Create.
  Although Create strictly needs only ``nutrient_id``, ``nutrient.display_name``, and ``unit.name``, I'm preserving the ``intake_guideline`` prop structure to be able to use the same CreateOrEdit component for Create.
- ``intake_guidelines`` for "Search for another" and "Clone existing"
- ``nutrient_categories`` to split up IntakeGuidelineNutrients into vitamins, minerals, macronutrients
- ``clone`` to conditionally display "Cloned from Foo" message
- Auth props to conditionally display View (for clone pages) and Clone/Create buttons

**Form:** See :ref:`Validation: Create or Update an Intake Guideline <validation-crud-intake-guideline>`

**UI:** IntakeGuidelineNutrients in table with columns:

- Nutrient name (static label)
- Nutrient amount (text input), prefilled either to zero or value from cloned Intake Guideline.
- Nutrient unit (static text)

**Links to:**

- View original (for Clone)
- Create blank (for Clone)
- Clone existing (for Create)
- Search
- Index

**Actions:**

- Store
- Cancel

Edit
^^^^

Purpose: update an existing Intake Guideline.

**Props:** 

.. code-block:: json

  {
    "intake_guideline": {
      "id": 0,
      "name": "Foo",
      "intake_guideline_nutrients": [
        {
          "id": 0,
          "intake_guideline_id": 0,
          "nutrient_id": 0,
          "rdi": 0.0,
          "nutrient": {
            "id": 0,
            "display_name": "Bar",
            "unit_id": 0,
            "nutrient_category_id": 0,
            "precision": 0,
            "display_order_id": 0,
            "unit": {
              "id": 0,
              "name": "Baz"
            }
          }
        }
      ]
    },
    "intake_guidelines": [
      {
        "id": 0,
        "name": "Boop"
      }
    ],
    "nutrient_categories": [
      {
        "id": 0,
        "name": "Blap"
      }
    ],
    "can_view": false,
    "can_clone": false,
    "can_delete": false,
    "can_create": false
  }

- ``intake_guideline`` to display current intake guideline information
- ``intake_guidelines`` for "Search for another" and "Clone existing"
- ``nutrient_categories`` to split up IntakeGuidelineNutrients into vitamins, minerals, macronutrients
- Auth props to conditionally display View, Clone, Delete, Create buttons.

**Form:** See :ref:`Validation: Create or Update an Intake Guideline <validation-crud-intake-guideline>`

**UI:** IntakeGuidelineNutrients in table with columns:

- Nutrient name (static label)
- Nutrient amount (text input), prefilled either to current value
- Nutrient unit (static text)

**Links to:**

- View
- Clone
- Delete
- Create
- Search
- Index

**Actions:**

- Store
- Cancel
