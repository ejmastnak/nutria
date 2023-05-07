Nutrient Profiles
=================

Some notes on computing nutrient profiles.

Basic API is a ``NutrientProfileController`` controller supporting the functions

- ``profileIngredient($ingredientID, $intakeGuidelineID)``
- ``profileMeal($mealID, $intakeGuidelineID)``
- ``profileFoodList($foodListID, $intakeGuidelineID)``

Each function returns an array of arrays with the structure

.. code-block:: php
    
    <?php
    array(
        array(
            "nutrient_id" => 1,
            "nutrient" => "Protein",
            "amount" => 25.0,
            "precision" => 0,
            "unit" => "g",
            "pdv" => 50,
            "nutrient_category_id" => 0
        ),
        array(
            "nutrient_id" => 2,
            "nutrient" => "Energy",
            "amount" => 2000,
            "precision" => 0,
            "unit" => "kcal",
            "pdv" => 100,
            "nutrient_category_id" => 0
        ),
    );


.. _profile-ingredient:

Profile Ingredient
------------------

For 100 grams of ingredient:

.. code-block:: sql

  select
    nutrients.id as nutrient_id,
    nutrients.name as nutrient,
    nutrients.nutrient_category_id as nutrient_category_id,
    nutrients.precision as precision,
    round(ingredient_nutrients.amount_per_100g, 3) as amount,
    units.name as unit,
    round((ingredient_nutrients.amount_per_100g / nullif(intake_guideline_nutrients.rdi, 0)) * 100, 2) as pdv
  from ingredient_nutrients
  inner join nutrients
    on nutrients.id
    = ingredient_nutrients.nutrient_id  
  inner join units
    on units.id
    = nutrients.unit_id
  inner join intake_guideline_nutrients
    on intake_guideline_nutrients.intake_guideline_id
    = :'intake_guideline_id'
    and intake_guideline_nutrients.nutrient_id
    = ingredient_nutrients.nutrient_id
  where ingredient_nutrients.ingredient_id=:'ingredient_id'
  order by nutrients.display_order_id;

Comments: 

- **PDV:** The ``nullif`` basically allows you to have Intake Guidelines where non-beneficial nutrients have no RDI and corresponding null PDV values in nutrient profiles.
  Use case: set a ``0`` or null ``rdi`` value for e.g. sugar, and than have a null PDV reported for sugar in nutrient profiles.
- **PDV:** Multiply by 100 to convert PDV to percentage

**For arbitrary quantity of ingredient**

Just scale nutrient mass density per gram (``ingredient_nutrients.amount_per_100g / 100``) by ingredient's mass in grams (``:'ingredient_mass_in_g'``)

.. code-block:: sql

  -- For arbitrary quantity of ingredient
  select round(((ingredient_nutrients.amount_per_100g / 100) * :'ingredient_mass_in_g'), 3) as amount

**Testing in Laravel Tinker**

.. code-block:: php

  <?php
  $i = App\Models\Ingredient::find(6435);
  $profile = App\Http\Controllers\NutrientProfileController::profileIngredient($i->id, 1);
  $profiles = App\Http\Controllers\NutrientProfileController::getNutrientProfilesOfIngredient($i->id);

.. _profile-meal:

Profile Meal
------------

.. code-block:: sql

  select
    nutrients.id as nutrient_id,
    nutrients.name as nutrient,
    nutrients.nutrient_category_id as nutrient_category_id,
    nutrients.precision as precision,
    round(sum((ingredient_nutrients.amount_per_100g / 100) * meal_ingredients.mass_in_grams), 3) as amount,
    units.name as unit,
    round(sum(ingredient_nutrients.amount_per_100g * meal_ingredients.mass_in_grams / nullif(intake_guideline_nutrients.rdi, 0)), 2) as pdv
  from ingredient_nutrients
  inner join meal_ingredients
    on ingredient_nutrients.ingredient_id
    = meal_ingredients.ingredient_id
    and meal_ingredients.meal_id
    = :'meal_id'
  inner join nutrients
    on nutrients.id
    = ingredient_nutrients.nutrient_id  
  inner join units
    on units.id
    = nutrients.unit_id
  inner join intake_guideline_nutrients
    on intake_guideline_nutrients.intake_guideline_id
    = :'intake_guideline_id'
    and intake_guideline_nutrients.nutrient_id
    = ingredient_nutrients.nutrient_id
  group by nutrients.id, units.name
  order by nutrients.display_order_id;

Comments: 

- **Nutrient amount:** for a given IngredientNutrient, just scale nutrient's mass density per gram (``ingredient_nutrients.amount_per_100g / 100``) by MealIngredient's mass in grams (``meal_ingredients.mass_in_grams``).
- **Nutrient amount:** we're basically summing the nutrient amount contributions of each MealIngredient.
  The result is one nutrient amount value (summed across all MealIngredients) for each nutrient---``sum(ingredient_nutrients.amount_per_100g * meal_ingredients.mass_in_grams / 100)`` (sum across MealIngredients) followed by ``group by nutrients.id`` (on scalar amount value for each Nutrient).
- **PDV:** same summation logic as for computing nutrient amount.
- **PDV:** same ``nullif`` function as for :ref:`profiling an Ingredient <profile-ingredient>`
- **PDV:** no division/multiplication by 100 because division by 100 (for normalizing nutrient mass density per 100 grams) and multiplication by 100 (to convert PDV to percentage) cancel out.

**For arbitrary quantity of meal**

Just scale by this meal's mass in grams (``:'this_meal_mass_in_grams'``) relative to default meal mass in grams (``meal.mass_in_grams``):

.. code-block:: sql

  -- For arbitrary quantity of meal
  select round(sum((ingredient_nutrients.amount_per_100g / 100) * meal_ingredients.mass_in_grams * :'this_meal_mass_in_grams' / meals.mass_in_grams), 3) as amount,

And you'd have to throw in a ``inner join meals on meals.id = :'meal_id'`` to get access to ``meals.mass_in_grams``.

**Testing in Laravel Tinker**

.. code-block:: php

  <?php
  $m = App\Models\Meal::find(1);
  $profile = App\Http\Controllers\NutrientProfileController::profileMeal($m->id, 1);
  $profiles = App\Http\Controllers\NutrientProfileController::getNutrientProfilesOfMeal($m->id);


Profile Food List
-----------------

It's split into two subqueries:

- Compute nutrient profile contribution of FoodListIngredients
- Compute nutrient profile contribution of FoodListMeals
- Concatenate the two subqueries with ``union all``
- For each nutrient, sum the FoodListIngredient and FoodListMeal contributions to nutrient amount and PDV.

Profile Food List Ingredients
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: sql

  select
    nutrients.id as nutrient_id,
    round(sum((ingredient_nutrients.amount_per_100g / 100) * food_list_ingredients.mass_in_grams), 3) as amount,
    round(sum(ingredient_nutrients.amount_per_100g * food_list_ingredients.mass_in_grams / nullif(intake_guideline_nutrients.rdi, 0)), 2) as pdv
  from ingredient_nutrients
  inner join food_list_ingredients
    on ingredient_nutrients.ingredient_id
    = food_list_ingredients.ingredient_id
    and food_list_ingredients.food_list_id
    = :'food_list_id'
  inner join nutrients
    on nutrients.id
    = ingredient_nutrients.nutrient_id  
  inner join intake_guideline_nutrients
    on intake_guideline_nutrients.intake_guideline_id
    = :'intake_guideline_id'
    and intake_guideline_nutrients.nutrient_id
    = ingredient_nutrients.nutrient_id
  group by nutrients.id

Comments:

- This is basically the same query as for :ref:`profiling a Meal <profile-meal>`, just with  ``food_list_ingredients`` replacing ``meal_ingredients``.
- But we only select Nutrient ID, amount, and PDV in this auxiliary subquery to avoid (more on principle than out of necesseity) the overhead of also querying unit name and ingredient name.

Profile Food List Meals
^^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: sql

  select
    nutrients.id as nutrient_id,
    round(sum((ingredient_nutrients.amount_per_100g / 100) * meal_ingredients.mass_in_grams * (food_list_meals.mass_in_grams / meals.mass_in_grams)), 3) as amount,
    round(sum(ingredient_nutrients.amount_per_100g * (meal_ingredients.mass_in_grams / nullif(intake_guideline_nutrients.rdi, 0)) * (food_list_meals.mass_in_grams / meals.mass_in_grams)), 2) as pdv
  from ingredient_nutrients
  inner join food_list_meals
    on food_list_meals.food_list_id
    = :'food_list_id'
  inner join meals
    on food_list_meals.meal_id
    = meals.id
  inner join meal_ingredients
    on ingredient_nutrients.ingredient_id
    = meal_ingredients.ingredient_id
    and meal_ingredients.meal_id
    = food_list_meals.meal_id
  inner join nutrients
    on nutrients.id
    = ingredient_nutrients.nutrient_id  
  inner join intake_guideline_nutrients
    on intake_guideline_nutrients.intake_guideline_id
    = :'intake_guideline_id'
    and intake_guideline_nutrients.nutrient_id
    = ingredient_nutrients.nutrient_id
  group by nutrients.id

Comments:

- **Nutrient amount:** for a given IngredientNutrient, just scale nutrient's mass density per gram (``ingredient_nutrients.amount_per_100g / 100``) by:

  - MealIngredient's mass in grams (``meal_ingredients.mass_in_grams``)
  - FoodListMeal's mass relative to corresponding Meal's default mass (``food_list_meals.mass_in_grams / meals.mass_in_grams``)

  Otherwise the summation follows same logic as for :ref:`Profiling a Meal <profile-meal>`.

- **PDV:** besides additional scaling by FoodListMeal's mass relative to corresponding Meal's default mass, the logic is the same as for :ref:`Profiling a Meal <profile-meal>`.

Combining the subqueries
^^^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: sql

  select
    nutrients.id as nutrient_id,
    nutrients.name,
    nutrients.nutrient_category_id as nutrient_category_id,
    nutrients.precision as precision,
    sum(result.amount) as amount,
    units.name,
    sum(result.pdv) as pdv
  from (
    -- FoodListIngredients subquery
    union all
    -- FoodListMeals subquery
  ) result
  inner join nutrients
    on nutrients.id
    = result.nutrient_id
  inner join units
    on units.id
    = nutrients.unit_id
  group by nutrients.id, units.name
  order by nutrients.display_order_id;

Comments:

- Nutrient and unit name are only added at this final stage.
- The union of the subqueries is arbitrarily called ``result``
- Sums of ``result.amount`` and ``result.pdv`` are grouped by ``nutrients.id`` to get desired effect of summing FoodListIngredient and FoodListMeal contributions to nutrient amount and PDV for each nutrient.

**Testing in Laravel Tinker**

.. code-block:: php

  <?php
  $fl = App\Models\FoodList::find(3);
  $profile = App\Http\Controllers\NutrientProfileController::profileFoodList($fl->id, 1);
  $profiles = App\Http\Controllers\NutrientProfileController::getNutrientProfilesOfFoodList($fl->id);
