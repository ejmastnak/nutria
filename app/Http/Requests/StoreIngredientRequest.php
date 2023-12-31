<?php

namespace App\Http\Requests;

use App\Models\Ingredient;
use App\Models\Nutrient;
use App\Rules\IsMassUnit;
use App\Rules\IsVolumeUnit;
use App\Rules\IsIngredientUnit;
use App\Rules\NutrientContentUnitIsValid;
use Illuminate\Foundation\Http\FormRequest;

class StoreIngredientRequest extends FormRequest
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
            'nutrient_content_unit_amount' => ['required', 'numeric', 'gt:0', config('validation.max_ingredient_amount')],
            'nutrient_content_unit' => ['required', 'array', new NutrientContentUnitIsValid],

            // Ingredient nutrients
            'ingredient_nutrients' => ['required', 'array', 'min:' . $numNutrients, 'max:' . $numNutrients],
            'ingredient_nutrients.*.nutrient_id' => ['required', 'distinct', 'integer', 'exists:nutrients,id'],
            'ingredient_nutrients.*.amount' => ['nullable', 'numeric', 'gte:0', config('validation.max_nutrient_amount')],

            // Density
            'density_mass_amount' => ['nullable', 'required_with:density_mass_unit_id,density_volume_amount,density_volume_unit_id', 'numeric', 'gt:0', config('validation.max_ingredient_amount')],
            'density_mass_unit_id' => ['nullable', 'required_with:density_mass_amount,density_volume_amount,density_volume_unit_id', 'integer', 'exists:units,id', new IsMassUnit],
            'density_volume_amount' => ['nullable', 'required_with:density_mass_amount,density_mass_unit_id,density_volume_unit_id', 'numeric', 'gt:0', config('validation.max_ingredient_amount')],
            'density_volume_unit_id' => ['nullable', 'required_with:density_mass_amount,density_mass_unit_id,density_volume_amount', 'integer', 'exists:units,id', new IsVolumeUnit],

            // Custom units
            'custom_units' => ['nullable', 'array', config('validation.max_custom_units')],
            'custom_units.*.id' => ['nullable', 'integer', 'exists:units,id', new IsIngredientUnit],
            'custom_units.*.name' => ['required', 'distinct', 'min:1', config('validation.max_name_length')],
            'custom_units.*.custom_unit_amount' => ['required', 'numeric', 'gt:0', config('validation.max_ingredient_amount')],
            'custom_units.*.custom_mass_amount' => ['required', 'numeric', 'gt:0', config('validation.max_ingredient_amount')],
            'custom_units.*.custom_mass_unit_id' => ['required', 'integer', 'exists:units,id', new IsMassUnit],
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
            'nutrient_content_unit_amount' => 'amount',
            'nutrient_content_unit_id' => 'unit_id',
            'ingredient_nutrients' => 'ingredient nutrients',
            'ingredient_nutrients.*.nutrient_id' => 'nutrient_id',
            'ingredient_nutrients.*.amount' => 'amount',
            'density_mass_amount' => 'amount',
            'density_mass_unit_id' => 'unit_id',
            'density_volume_amount' => 'amount',
            'density_volume_unit_id' => 'unit_id',
            'custom_units.*.name' => 'name',
            'custom_units.*.custom_unit_amount' => 'amount',
            'custom_units.*.custom_mass_amount' => 'weight',
            'custom_units.*.custom_mass_unit_id' => 'unit_id',
        ];
    }

}
