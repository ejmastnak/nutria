<?php

namespace App\Http\Requests;

use App\Models\FoodList;
use App\Rules\HasIngredientsIfNoMeals;
use App\Rules\HasMealsIfNoIngredients;
use App\Rules\IngredientUnitIsConsistent;
use App\Rules\MealUnitIsConsistent;
use App\Rules\IngredientOwnedByUser;
use App\Rules\MealOwnedByUser;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFoodListRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $foodList = $this->route('food_list');
        $user = $this->user();
        return $foodList && $user && $user->can('update', $foodList);
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

            // Food list ingredients
            'food_list_ingredients' => ['nullable', 'array', config('validation.max_food_list_ingredients'), new HasIngredientsIfNoMeals],
            'food_list_ingredients.*.id' => ['nullable', 'integer', 'exists:food_list_ingredients,id'],
            'food_list_ingredients.*.ingredient_id' => ['required', 'integer', 'exists:ingredients,id', new IngredientOwnedByUser],
            'food_list_ingredients.*.amount' => ['required', 'numeric', 'gt:0', config('validation.max_ingredient_amount')],
            'food_list_ingredients.*.unit_id' => ['required', 'integer', 'exists:units,id'],
            'food_list_ingredients.*' => [new IngredientUnitIsConsistent],

            // Food list meals
            'food_list_meals' => ['nullable', 'array', config('validation.max_food_list_meals'), new HasMealsIfNoIngredients],
            'food_list_meals.*.id' => ['nullable', 'integer', 'exists:food_list_meals,id'],
            'food_list_meals.*.meal_id' => ['required', 'integer', 'exists:meals,id', new MealOwnedByUser],
            'food_list_meals.*.amount' => ['required', 'numeric', 'gt:0', config('validation.generic_max_amount')],
            'food_list_meals.*.unit_id' => ['required', 'integer', 'exists:units,id'],
            'food_list_meals.*' => [new MealUnitIsConsistent],

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
            'food_list_ingredients.*.ingredient_id' => 'ingredient_id',
            'food_list_ingredients.*.amount' => 'amount',
            'food_list_ingredients.*.unit_id' => 'unit_id',
            'food_list_ingredients.*' => 'food list ingredient',
            'food_list_meals.*.meal_id' => 'meal_id',
            'food_list_meals.*.amount' => 'amount',
            'food_list_meals.*.unit_id' => 'unit_id',
            'food_list_meals.*' => 'food list meal',
        ];
    }

}
