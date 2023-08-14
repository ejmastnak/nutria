<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Ingredient;
use App\Models\Nutrient;

class UpdateIngredientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * This is delegated to controller.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $num_nutrients = Nutrient::count();
        return [
            'name' => ['required', 'min:1', 'max:500'],
            'ingredient_category_id' => ['required', 'integer', 'exists:ingredient_categories,id'],
            'density_g_per_ml' => ['nullable', 'numeric', 'gt:0'],
            'ingredient_nutrients' => ['required', 'array', 'max:' . $num_nutrients],
            'ingredient_nutrients.*.id' => ['required', 'integer'],
            'ingredient_nutrients.*.nutrient_id' => ['required', 'distinct', 'integer', 'exists:nutrients,id'],
            'ingredient_nutrients.*.amount_per_100g' => ['required', 'numeric', 'gte:0'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'ingredient_category_id.required' => 'An ingredient category is required.',
            'ingredient_category_id.integer' => 'Use an existing ingredient category.',
            'ingredient_category_id.exists' => 'Use an existing ingredient category.',
            'density_g_per_ml.numeric' => 'The density should be a positive number.',
            'density_g_per_ml.gt' => 'The density should be greater than zero.',
            'ingredient_nutrients.*.amount_per_100g.required' => 'The nutrient content is required.',
            'ingredient_nutrients.*.amount_per_100g.numeric' => 'The nutrient amount must be a nonnegative number.',
            'ingredient_nutrients.*.amount_per_100g.gte' => 'The nutrient amount must be a nonnegative number.',
        ];
    }
}
