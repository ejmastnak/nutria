<?php

namespace App\Rules;

use Closure;
use App\Models\Meal;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

class MealOwnedByUser implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $meal = Meal::find($value);
        if (is_null($meal)) {
            $fail('The :attribute is not valid.');
            return;
        }

        $user = Auth::user();
        $userId = $user ? $user->id : null;
        if (is_null($userId) || $userId !== $meal->user_id) $fail('You do not own this meal.');
    }
}
