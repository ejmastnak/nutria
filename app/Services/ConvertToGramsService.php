<?php
namespace App\Services;

use App\Models\Ingredient;
use App\Models\IngredientNutrient;
use App\Models\Unit;
use App\Services\ComputeDensityService;
use App\Services\ConvertToGramsService;

/**
 *  Used to convert amounts of Ingredients, MealIngredients,
 *  FoodListIngredients, FoodListMeal, etc. from user-specified units to grams,
 *  e.g. when creating Meals and FoodLists.
 */
class ConvertToGramsService
{
    /**
     *  Returns the number of grams equal to `amount` of the Unit with
     *  `unit_id`, or `null` if the conversion is not possible.
     */
    public function convertToGrams(int $unitId, float $amount, ?int $ingredientId, ?int $mealId): ?float
    {
        $unit = Unit::find($unitId);
        if (is_null($unit)) return null;

        // For mass units
        if (!is_null($unit->g)) return $amount * $unit->g;

        $ingredient = Ingredient::find($ingredientId);
        if (is_null($ingredient)) return null;

        // For volume units
        if (!is_null($unit->ml)) {
            if (is_null($ingredient->density_g_ml)) return null;
            // Convert volume amount to ml, then from ml to grams
            return $amount * $unit->ml * $ingredient->density_g_ml;
        }

        // For custom ingredient units
        if (!is_null($unit->ingredient_id)) {
            if ($unit->ingredient_id != $ingredientId) return null;
            return $amount * $unit->custom_g;
        }

        // For custom meal units
        if (!is_null($unit->meal_id)) {
            if ($unit->meal_id != $mealId) return null;
            return $amount * $unit->custom_g;
        }

        return null;
    }

}
