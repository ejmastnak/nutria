<?php

namespace App\Rules;

use Closure;
use App\Models\Unit;
use Illuminate\Contracts\Validation\ValidationRule;

class IsIngredientUnit implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $unit = Unit::find($value);
        if (is_null($unit)) {
            $fail('The :attribute was not a recognized as a unit.');
            return;
        }
        if (is_null($unit->ingredient_id)) $fail('The :attribute must be an ingredient-specific unit.');
    }
}
