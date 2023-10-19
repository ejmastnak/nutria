<?php

namespace App\Http\Requests;

use App\Models\Ingredient;
use App\Models\Nutrient;
use App\Rules\IsMassUnit;
use App\Rules\IsVolumeUnit;
use Illuminate\Foundation\Http\FormRequest;

class IngredientStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && $user->can('create', Ingredient::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $numNutrients = Nutrient::count();
        return [
            'name' => ['required', 'min:1', config('validation.max_name_length')],
            'ingredient_category_id' => ['nullable', 'integer', 'exists:ingredient_categories,id'],

            // Ingredient nutrients
            'ingredient_nutrients' => ['required', 'array', 'min:' . $numNutrients, 'max:' . $numNutrients],
            'ingredient_nutrients.*.nutrient_id' => ['required', 'distinct', 'integer', 'exists:nutrients,id'],
            'ingredient_nutrients.*.amount_per_100g' => ['required', 'numeric', 'gte:0', config('validation.max_nutrient_amount')],

            // Density
            'density_mass_unit_id' => ['nullable', 'integer', 'exists:units,id', new IsMassUnit],
            'density_mass_amount' => ['required_with:density_mass_unit_id', 'numeric', 'gt:0', config('validation.max_ingredient_amount')],
            'density_volume_unit_id' => ['required_with:density_mass_unit_id', 'integer', 'exists:units,id', new IsVolumeUnit],
            'density_volume_amount' => ['required_with:density_mass_unit_id', 'numeric', 'gt:0', config('validation.max_ingredient_amount')],

            // Custom units
            'custom_units' => ['nullable', 'min:1', config('validation.max_custom_units')],
            'custom_units*.name' => ['required', 'min:1', config('validation.max_name_length')],
            'custom_units*.custom_unit_amount' => ['required', 'numeric', 'gt:0', config('validation.max_ingredient_amount')],
            'custom_units*.custom_mass_amount' => ['required', 'numeric', 'gt:0', config('validation.max_ingredient_amount')],
            'custom_units*.custom_mass_unit_id' => ['required', 'integer', 'exists:units,id', new IsMassUnit],
        ];
    }
}
