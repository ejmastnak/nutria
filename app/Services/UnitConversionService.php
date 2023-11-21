<?php
namespace App\Services;

use App\Models\Ingredient;
use App\Models\Unit;

/**
 *  Used to convert amounts of Ingredients, MealIngredients,
 *  FoodListIngredients, FoodListMeal, etc. from user-specified units to grams,
 *  e.g. when creating Meals and FoodLists.
 */
class UnitConversionService
{
    /**
     *  Returns the number of grams equal to `amount` of the Unit with
     *  `unit_id`, or `null` if the conversion is not possible.
     */
    public static function convertToGrams(float $amount, int $unitId, ?int $ingredientId, ?int $mealId): ?float
    {
        $unit = Unit::find($unitId);
        if (is_null($unit)) throw new \ValueError("Could not find Unit with id " . $unitId);

        // For mass units
        if (!is_null($unit->g)) return $amount * $unit->g;

        // For volume units
        if (!is_null($unit->ml)) {
            $ingredient = Ingredient::find($ingredientId);
            if (!is_null($ingredient) && !is_null($ingredient->density_g_ml)) {
                // Convert volume amount to ml, then from ml to grams
                return $amount * $unit->ml * $ingredient->density_g_ml;
            }
        }

        // For custom ingredient units
        if (!is_null($unit->ingredient_id) && $unit->ingredient_id === $ingredientId) {
            return $amount * $unit->custom_grams;
        }

        // For custom meal units
        if (!is_null($unit->meal_id) && $unit->meal_id === $mealId) {
            return $amount * $unit->custom_grams;
        }

        throw new \ValueError("Unable to convert inputed amount and unit to grams.");
    }

}
