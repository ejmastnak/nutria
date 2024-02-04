<?php

namespace App\Http\Requests;

use App\Models\Meal;
use App\Rules\IngredientUnitIsConsistent;
use App\Rules\IngredientOwnedByUser;
use Illuminate\Foundation\Http\FormRequest;

class StoreMealRequest extends FormRequest
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
            'description' => ['nullable', 'min:1', config('validation.max_description_length')],

            // Meal ingredients
            'meal_ingredients' => ['required', 'array', 'min:1', config('validation.max_meal_ingredients')],
            'meal_ingredients.*.id' => ['nullable', 'integer', 'exists:meal_ingredients,id'],
            'meal_ingredients.*.ingredient_id' => ['required', 'integer', 'exists:ingredients,id', new IngredientOwnedByUser],
            'meal_ingredients.*.amount' => ['required', 'numeric', 'gt:0', config('validation.max_ingredient_amount')],
            'meal_ingredients.*.unit_id' => ['required', 'integer', 'exists:units,id'],
            'meal_ingredients.*' => [new IngredientUnitIsConsistent],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'meal_ingredients.*.ingredient_id' => 'ingredient_id',
            'meal_ingredients.*.amount' => 'amount',
            'meal_ingredients.*.unit_id' => 'unit_id',
            'meal_ingredients.*' => 'meal ingredient',
        ];
    }

}
