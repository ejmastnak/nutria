<?php

namespace App\Rules;

use Closure;
use App\Models\Unit;
use App\Models\Meal;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 *  Ensures a FoodListMeal's amount is specified in a unit supported by the
 *  underlying Meal.
 */
class MealUnitIsConsistent implements ValidationRule
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

        $meal = Meal::find($value['meal_id']);
        if (is_null($meal)) {
            $fail("The :attribute's meal was not recognized.");
            return;
        }

        // Allow any mass unit
        if (!is_null($unit->g)) return;

        // Allow meal custom units only for a matching meal
        else if (!is_null($unit->meal_id)) {
            if ($unit->meal_id === $meal->id) return;
            else {
                $fail("The meal does not support the unit " . $unit->name . " .");
                return;
            }
        }

        // Fail all other cases
        else $fail("The meal does not support the unit " . $unit->name . " .");
    }
}
