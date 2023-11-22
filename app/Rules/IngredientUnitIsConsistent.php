<?php

namespace App\Rules;

use Closure;
use App\Models\Unit;
use App\Models\Ingredient;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 *  Ensures a MealIngredient or FoodListIngredient's amount is specified in a
 *  unit supported by the underlying Ingredient. Expects as input a PHP array
 *  with a `unit_id` and `ingredient_id` key.
 */
class IngredientUnitIsConsistent implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $unit = Unit::find($value['unit_id']);
        if (is_null($unit)) {
            $fail("The :attribute's unit was not recognized.");
            return;
        }

        $ingredient = Ingredient::find($value['ingredient_id']);
        if (is_null($ingredient)) {
            $fail("The :attribute's ingredient was not recognized.");
            return;
        }

        // Allow any mass unit
        if (!is_null($unit->g)) return;

        // Allow volume units if underlying Ingredient has a density
        else if (!is_null($unit->ml)) {
            if (!is_null($ingredient->density_g_ml)) return;
            else {
                $fail("The ingredient does not have a density, so its amount cannot be expressed in a unit of volume.");
                return;
            }
        }

        // Allow ingredient custom units only for a matching ingredient
        else if (!is_null($unit->ingredient_id)) {
            if ($unit->ingredient_id === $ingredient->id) return;
            else {
                $fail("The ingredient does not support the unit " . $unit->name . " .");
                return;
            }
        }

        // Fail all other cases
        else $fail("The ingredient does not support the unit " . $unit->name . " .");
    }
}
