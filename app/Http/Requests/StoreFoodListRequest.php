<?php

namespace App\Http\Requests;

use App\Models\FoodList;
use App\Rules\HasIngredientsIfNoMeals;
use App\Rules\HasMealsIfNoIngredients;
use App\Rules\IngredientUnitIsConsistent;
use App\Rules\MealUnitIsConsistent;
use Illuminate\Foundation\Http\FormRequest;

class StoreFoodListRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && $user->can('create', FoodList::class);
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

            // Food list ingredients
            'food_list_ingredients' => ['nullable', 'array', config('validation.max_food_list_ingredients'), new HasIngredientsIfNoMeals],
            'food_list_ingredients.*.id' => ['nullable', 'integer', 'exists:food_list_ingredients,id'],
            'food_list_ingredients.*.ingredient_id' => ['required', 'integer', 'exists:ingredients,id'],
            'food_list_ingredients.*.amount' => ['required', 'numeric', 'gt:0'],
            'food_list_ingredients.*.unit_id' => ['required', 'integer', 'exists:units,id'],
            'food_list_ingredients.*' => [new IngredientUnitIsConsistent],

            // Food list meals
            'food_list_meals' => ['nullable', 'array', config('validation.max_food_list_meals'), new HasMealsIfNoIngredients],
            'food_list_meals.*.id' => ['nullable', 'integer', 'exists:food_list_meals,id'],
            'food_list_meals.*.meal_id' => ['required', 'integer', 'exists:meals,id'],
            'food_list_meals.*.amount' => ['required', 'numeric', 'gt:0'],
            'food_list_meals.*.unit_id' => ['required', 'integer', 'exists:units,id'],
            'food_list_meals.*' => [new MealUnitIsConsistent],

        ];
    }
}
