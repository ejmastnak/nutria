<?php

namespace App\Rules;

use Closure;
use App\Models\Unit;
use App\Models\Ingredient;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\DataAwareRule;

/**
 *  Ensures an Ingredient's `ingredient_nutrient_amount_unit` is consistent
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
        // An mass or volume existing unit
        if (!is_null($value['id'])) {

            $unit = Unit::find($value['id']);
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
        }

        // (Presumably) a custom unit: pass if custom_units is non-empty,
        // and contains an entry matching this $value.
        if ($value['custom_grams']) {
            if (is_null($this->data['custom_units']) || count($this->data['custom_units']) === 0) {
                $fail("The unit is not valid.");
                return;
            }
            // Check for a matching unit entry in ingredient's custom units
            foreach ($this->data['custom_units'] as $customUnit) {
                if ($this->unitsAreEqual($customUnit, $value)) return;
            }
        }

        $fail("The unit is not valid.");
    }

    // Helper function to check if two units are equal
    private function unitsAreEqual(array $a, array $b) {
        // For existing units
        if (isset($a['id']) && isset($b['id'])) return $a['id'] === $b['id'];
        // For newly created custom units
        else if (is_null($a['id']) && is_null($b['id'])) {
            return ($a['name'] === $b['name'] && $a['custom_unit_amount'] === $b['custom_unit_amount'] && $a['custom_mass_amount'] === $b['custom_mass_amount'] && $a['custom_mass_unit_id'] === $b['custom_mass_unit_id']);
        }
        // Comparing a newly-created custom unit to an existing unit
        else return false;
    }

}
