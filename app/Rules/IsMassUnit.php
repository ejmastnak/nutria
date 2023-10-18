<?php

namespace App\Rules;

use Closure;
use App\Models\Unit;
use Illuminate\Contracts\Validation\ValidationRule;

class IsMassUnit implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $unit = Unit::find($value);
        if (is_null($unit)) $fail('The :attribute was not a recognized as a unit.');
        if (is_null($unit->g)) $fail('The :attribute must be unit of mass.');
    }
}
