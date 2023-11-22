<?php

namespace App\Rules;

use Closure;
use App\Models\Unit;
use App\Models\Ingredient;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\DataAwareRule;

/**
 *  Equivalent of IngredientUnitIsConsistent, but instead relying on
 *  DataAwareRule to access request data. Intended for use with
 *  IngredientIntakeRecordRequests.
 */
class DataAwareIngredientUnitIsConsistent implements ValidationRule, DataAwareRule
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
        $unit = Unit::find($data['unit_id']);
        if (is_null($unit)) {
            $fail("The :attribute's unit was not recognized.");
            return;
        }

        $ingredient = Ingredient::find($data['ingredient_id']);
        if (is_null($ingredient)) {
            $fail("The :attribute's ingredient was not recognized.");
            return;
        }

        // Allow any mass unit
        if (!is_null($unit->g)) return;

        // Allow volume units if underlying Ingredient has a density
        else if (!is_null($unit->ml)) {
            if (!is_null($ingredient->density_g_ml)) return;
            else {
                $fail("The ingredient does not have a density, so its amount cannot be expressed in a unit of volume.");
                return;
            }
        }

        // Allow ingredient custom units only for a matching ingredient
        else if (!is_null($unit->ingredient_id)) {
            if ($unit->ingredient_id === $ingredient->id) return;
            else {
                $fail("The ingredient does not support the unit " . $unit->name . " .");
                return;
            }
        }

        // Fail all other cases
        else $fail("The ingredient does not support the unit " . $unit->name . " .");
    }
}
