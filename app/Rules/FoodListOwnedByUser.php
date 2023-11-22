<?php

namespace App\Rules;

use Closure;
use App\Models\FoodList;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

class FoodListOwnedByUser implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $foodList = FoodList::find($value);
        if (is_null($foodList)) {
            $fail('The :attribute is not valid.');
            return;
        }

        $user = Auth::user();
        $userId = $user ? $user->id : null;
        if (is_null($userId) || $userId !== $foodList->user_id) $fail('You do not own this food list.');
    }
}
