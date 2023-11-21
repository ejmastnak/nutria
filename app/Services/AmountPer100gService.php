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
     *  `unit_id`.
     */
    public static function computeAmountPer100g(float $nutrientAmount, float $ingredientAmount, int $ingredientAmountUnitId, ?float $ingredientDensityGMl): ?float
    {
        $unit = Unit::find($ingredientAmountUnitId);
        if (is_null($unit)) throw new \ValueError("Could not find Unit with id " . $unitId);

        // For mass units
        if (!is_null($unit->g)) return $nutrientAmount * (100 / ($unit->g * $ingredientAmount));

        // For volume units
        if (!is_null($unit->ml) && !is_null($ingredientDensityGMl)) {
            // Convert volume amount to ml, then from ml to grams
            return $nutrientAmount * (100 / ($ingredientAmount * $unit->ml * $ingredientDensityGMl));
        }

        // For custom ingredient units
        if (!is_null($unit->ingredient_id)) {
            return $nutrientAmount * (100 / ($unit->custom_grams * $ingredientAmount));
        }

        throw new \ValueError("Unable to compute amount of nutrient per 100 g of ingredient.");
    }

}
