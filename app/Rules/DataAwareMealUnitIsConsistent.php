<?php

namespace App\Rules;

use Closure;
use App\Models\Unit;
use App\Models\Meal;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\DataAwareRule;

/**
 *  Equivalent of MealUnitIsConsistent, but instead relying on DataAwareRule to
 *  access request data. Intended for use with MealIntakeRecordRequests.
 */
class DataAwareMealUnitIsConsistent implements ValidationRule, DataAwareRule
{

    /**
     * All of the data under validation.
     *
     * @var array<string, mixed>
     */
    protected $data = [];

    /**
     * Set the data under validation.
     *
     * @param  array<string, mixed>  $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $unit = Unit::find($data['unit_id']);
        if (is_null($unit)) {
            $fail("The :attribute's unit was not recognized.");
            return;
        }

        // Allow any mass unit
        if (!is_null($unit->g)) return;

        $meal = Meal::find($data['meal_id']);
        if (is_null($meal)) {
            $fail("The :attribute's meal was not recognized.");
            return;
        }

        // Allow only the meal unit for the matching meal
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
