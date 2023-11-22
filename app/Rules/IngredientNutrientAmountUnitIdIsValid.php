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
class IngredientNutrientAmountUnitIdIsValid implements DataAwareRule, ValidationRule
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
        $unit = Unit::find($value);
        if (is_null($unit)) {
            $fail("The ingredient's unit was not recognized.");
            return;
        }

        // Allow any mass unit
        if (!is_null($unit->g)) return;

        // Allow volume units if underlying Ingredient has a density
        else if (!is_null($unit->ml)) {
            if (is_null($this->data['density_mass_unit_id']) || is_null($this->data['density_mass_amount']) || is_null($this->data['density_volume_unit_id']) || is_null($this->data['density_volume_unit_id'])) {
                $fail("The ingredient does not have a valid density, so its amount cannot be expressed in a unit of volume.");
                return;
            }
            else return;
        }

        // A custom unit
        else if (!is_null($unit->ingredient_id)) {
            if (is_null($this->data['custom_units']) || count($this->data['custom_units']) === 0) {
                $fail("The ingredient's unit is not valid.");
                return;
            }

            // Check for a matching unit entry in ingredient's custom units
            $matchingCustomUnitExists = false;
            foreach ($this->data['custom_units'] as $customUnit) {
                if ($customUnit['id'] === $value) {
                    $matchingCustomUnitExists = true;
                    break;
                }
            }
            if (!$matchingCustomUnitExists) {
                $fail("This ingredient unit is not valid. Perhaps it was just deleted?");
                return;
            }
        }

        // Fail all other cases
        else $fail("The ingredient's unit is not valid.");
    }
}
