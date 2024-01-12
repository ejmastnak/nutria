<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidStoreAndLogMealIntakeRecordUnit implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * $value is an array (representing the unit for a meal intake record's
     * amount) with keys `id` and `name`.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (isset($value['id'])) {
            // If not null, `unit.id` must be the id of a mass Unit
            $unit = Unit::find($value['id']);
            if (is_null($unit)) {
                $fail("The unit was not recognized.");
                return;
            } else {
                if (is_null($unit->g)) {
                    $fail("The unit must be a mass unit.");
                    return;
                }
                return;  // pass validation
            }
        } else if (isset($value['name'])) {
            // If `unit.id` is null, then `unit.name` must `"meal"`
            if ($value['name'] === 'meal') return;
        }

        $fail("The unit was not recognized.");
    }
}
