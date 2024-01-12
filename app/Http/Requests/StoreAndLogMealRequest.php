<?php

namespace App\Http\Requests;

use App\Models\Meal;
use App\Rules\IngredientUnitIsConsistent;
use App\Rules\IngredientOwnedByUser;
use App\Rules\ValidStoreAndLogMealIntakeRecordUnit;
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
            'meal.name' => ['required', 'min:1', config('validation.max_name_length')],
            'meal.meal_ingredients' => ['required', 'array', 'min:1', config('validation.max_meal_ingredients')],
            'meal.meal_ingredients.*.id' => ['nullable', 'integer', 'exists:meal_ingredients,id'],
            'meal.meal_ingredients.*.ingredient_id' => ['required', 'integer', 'exists:ingredients,id', new IngredientOwnedByUser],
            'meal.meal_ingredients.*.amount' => ['required', 'numeric', 'gt:0', config('validation.max_ingredient_amount')],
            'meal.meal_ingredients.*.unit_id' => ['required', 'integer', 'exists:units,id'],
            'meal.meal_ingredients.*' => [new IngredientUnitIsConsistent],

            // Meal intake record
            'meal_intake_record.amount' => ['required', 'numeric', 'gt:0', config('validation.generic_max_amount')],
            'meal_intake_record.unit' => ['required', 'array', 'required_array_keys:id,name', new ValidStoreAndLogMealIntakeRecordUnit],
            'meal_intake_record.date' => ['required', 'string', 'date_format:Y-m-d'],
            'meal_intake_record.time' => ['required', 'string', 'date_format:H:i,H:i:s'],
            'meal_intake_record.date_time_utc' => ['required', 'string', 'date_format:Y-m-d H:i,Y-m-d H:i:s'],
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
            'meal.name' => 'meal name',
            'meal.meal_ingredients.*.ingredient_id' => 'ingredient_id',
            'meal.meal_ingredients.*.amount' => 'amount',
            'meal.meal_ingredients.*.unit_id' => 'unit_id',
            'meal.meal_ingredients.*' => 'meal ingredient',

            'meal_intake_record.amount' => 'amount',
            'meal_intake_record.unit' => 'unit',
            'meal_intake_record.date' => 'date',
            'meal_intake_record.time' => 'time',
            'meal_intake_record.date_time_utc' => 'combined date and time',
        ];
    }

}
