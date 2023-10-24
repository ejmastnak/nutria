<?php

namespace App\Rules;

use Closure;
use App\Models\Unit;
use App\Models\Ingredient;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\DataAwareRule;

/**
 *  Ensures an Ingredient's `ingredient_nutrient_amount_unit_id` is consistent
 *  with the underlying Ingredient.
 */
class IngredientNutrientAmountUnitIsValid implements DataAwareRule, ValidationRule
{
    /**
     * All of the data under validation.
     *
     * @var array<string, mixed>
     */
    protected $data = [];

    /**
     * Set the data under validation.
     *
     * @param  array<string, mixed>  $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Pass if ingredient_nutrient_amount_unit.id is not null
        if (!is_null($value['id'])) return;

        if (is_null($data['custom_units']) || count($data['custom_units']) === 0) $fail("The ingredient's unit is not valid.");

        // Check for a matching unit entry in ingredient's custom units
        $matchingCustomUnitExists = false;
        foreach ($data['custom_units'] as $customUnit) {
            if ($customUnit['name'] === $value['name'] && $customUnit['custom_unit_amount'] === $value['custom_unit_amount'] && $customUnit['custom_mass_amount'] === $value['custom_mass_amount'] && $customUnit['custom_mass_unit_id'] === $value['custom_mass_unit_id']) {
                $matchingCustomUnitExists = true;
                break;
            }
        }
        if (!$matchingCustomUnitExists) $fail("This ingredient unit is not valid.");
    }
}
