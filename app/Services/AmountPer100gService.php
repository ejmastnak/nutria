<?php
namespace App\Services;

use App\Models\Ingredient;
use App\Models\Unit;
use App\Services\ConvertToGramsService;

/**
 *  Used to convert an ingredient's nutrient content per user-specified amount
 *  of ingredient to corresponding nutrient content per 100 grams of
 *  ingredient.
 */
class AmountPer100gService
{
    /**
     *  Returns the number of grams equal to `amount` of the Unit with
     *  `unit_id`, or `null` if the conversion is not possible.
     */
    public static function computeAmountPer100g(float $nutrientAmount, float $ingredientAmount, array $ingredientAmountUnit, ?float $ingredientDensityGMl): ?float
    {

        if (!is_null($ingredientAmountUnit['id'])) {
            $unit = Unit::find($unitId);
            if (is_null($unit)) return null;

            // For mass units
            if (!is_null($unit->g)) return $nutrientAmount * (100 / ($unit->g * $ingredientAmount));

            // For volume units
            if (!is_null($unit->ml)) {
                if (is_null($ingredientDensityGMl)) return null;
                // Convert volume amount to ml, then from ml to grams
                return $nutrientAmount * (100 / ($ingredientAmount * $unit->ml * $ingredientDensityGMl));
            }

            // For custom ingredient units
            if (!is_null($unit->ingredient_id)) {
                return $nutrientAmount * (100 / ($unit->custom_g * $ingredientAmount));
            }
        } else {
            $customGrams = ConvertToGramsService::convertToGrams($ingredientAmountUnit['custom_mass_unit_id'], $ingredientAmountUnit['custom_mass_amount'], null, null);
            if (is_null($customGrams)) return null;
            $customGrams /= $ingredientAmountUnit['custom_unit_amount'];
            return $nutrientAmount * (100 / ($customGrams * $ingredientAmount));
        }

        return null;
    }

}
