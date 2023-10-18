<?php

namespace App\Http\Requests;

use App\Models\Meal;
use App\Rules\IngredientUnitIsConsistent;
use Illuminate\Foundation\Http\FormRequest;

class MealStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && $user->can('create', Meal::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'min:1', config('validation.max_name_length')],

            // Meal ingredients
            'meal_ingredients' => ['required', 'array', 'min:1', config('validation.max_meal_ingredients')],
            'meal_ingredients.*.id' => ['nullable', 'integer', 'exists:meal_ingredients,id'],
            'meal_ingredients.*.ingredient_id' => ['required', 'integer', 'exists:ingredients,id'],
            'meal_ingredients.*.amount' => ['required', 'numeric', 'gt:0'],
            'meal_ingredients.*.unit_id' => ['required', 'integer', 'exists:units,id'],
            'meal_ingredients.*' => [new IngredientUnitIsConsistent],
        ];
    }
}
