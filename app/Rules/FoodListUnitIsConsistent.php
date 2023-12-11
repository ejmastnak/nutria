<?php

namespace App\Rules;

use Closure;
use App\Models\Unit;
use App\Models\FoodList;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 *  Ensures a FoodListIntakeRecord's amount is specified in a unit supported by
 *  the underlying FoodList. Expects as input a PHP array with a `unit_id` and
 *  `food_list_id` key.
 */
class FoodListUnitIsConsistent implements ValidationRule
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

        $foodList = FoodList::find($value['food_list_id']);
        if (is_null($foodList)) {
            $fail("The :attribute's food list was not recognized.");
            return;
        }

        // Allow only the food list unit for the matching food list
        if (!is_null($unit->food_list_id)) {
            if ($unit->food_list_id === $foodList->id) return;
            else {
                $fail("The food list does not support the unit " . $unit->name . " .");
                return;
            }
        }

        // Fail all other cases
        else $fail("The food list does not support the unit " . $unit->name . " .");
    }
}
