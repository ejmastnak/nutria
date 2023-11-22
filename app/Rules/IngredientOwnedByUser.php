<?php

namespace App\Rules;

use Closure;
use App\Models\Ingredient;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

class IngredientOwnedByUser implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $ingredient = Ingredient::find($value);
        if (is_null($ingredient)) {
            $fail('The :attribute is not valid.');
            return;
        }

        // Allow USDA ingredients
        if (is_null($ingredient->user_id)) return;

        $user = Auth::user();
        $userId = $user ? $user->id : null;
        if (is_null($userId) || $userId !== $ingredient->user_id) $fail('You do not own this ingredient.');
    }
}
