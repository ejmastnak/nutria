<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IsVolumeUnit implements ValidationRule
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
        if (is_null($unit->ml)) $fail('The :attribute must be unit of volume.');
    }
}
