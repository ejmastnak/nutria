Nutrient Profiles
=================

Some notes on computing nutrient profiles.

I should have a ``NutrientProfileController`` controller supporting the functions

- ``profileIngredient(Ingredient $ingredient, $mass_in_grams = 100)``
- ``profileMeal(Meal) $meal)``
- ``profileFoodList(FoodList) $foodList)``

Each function returns an array of arrays with the structure

.. code-block:: php
    
    <?php
    array(
        array(
            "nutrient" => "Protein",
            "amount" => 25.0,
            "unit" => "g",
            "pdv" => "50"
        ),
        array(
            "nutrient" => "Energy",
            "amount" => 2000,
            "unit" => "kcal",
            "pdv" => "100"
        ),
    );
