<?php

namespace App\Http\Requests;

use App\Models\Meal;
use App\Rules\IngredientUnitIsConsistent;
use App\Rules\IngredientOwnedByUser;
use Illuminate\Foundation\Http\FormRequest;

class StoreAndLogMealRequest extends FormRequest
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
            // Meal
            'name' => ['required', 'min:1', config('validation.max_name_length')],
            'description' => ['nullable', 'min:1', config('validation.max_description_length')],
            'meal_ingredients' => ['required', 'array', 'min:1', config('validation.max_meal_ingredients')],
            'meal_ingredients.*.id' => ['nullable', 'integer', 'exists:meal_ingredients,id'],
            'meal_ingredients.*.ingredient_id' => ['required', 'integer', 'exists:ingredients,id', new IngredientOwnedByUser],
            'meal_ingredients.*.amount' => ['required', 'numeric', 'gt:0', config('validation.max_ingredient_amount')],
            'meal_ingredients.*.unit_id' => ['required', 'integer', 'exists:units,id'],
            'meal_ingredients.*' => [new IngredientUnitIsConsistent],

            // Meal intake record
            'date' => ['required', 'string', 'date_format:Y-m-d'],
            'time' => ['required', 'string', 'date_format:H:i,H:i:s'],
            'date_time_utc' => ['required', 'string', 'date_format:Y-m-d H:i,Y-m-d H:i:s'],
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
            // Meal
            'name' => 'meal name',
            'meal_ingredients.*.ingredient_id' => 'ingredient_id',
            'meal_ingredients.*.amount' => 'amount',
            'meal_ingredients.*.unit_id' => 'unit_id',
            'meal_ingredients.*' => 'meal ingredient',

            // Meal intake record
            'date' => 'date',
            'time' => 'time',
            'date_time_utc' => 'combined date and time',
        ];
    }

}
