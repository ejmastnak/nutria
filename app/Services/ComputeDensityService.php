<?php
namespace App\Services;

use App\Models\Unit;

/**
 *  Used to convert a user-specified density (in arbitrary units of mass per
 *  arbitrary units of volume) to density in grams per milliliter (e.g. when
 *  creating Ingredients).
 */
class ComputeDensityService
{

    public function convertToGrams(int $densityMassUnitId, float $densityMassAmount, int $densityVolumeUnitId, float $densityVolumeAmount): ?float
    {
        $massUnit = Unit::find($densityMassUnitId);
        if (is_null($massUnit) || is_null($massUnit->g)) return null;

        $volumeUnit = Unit::find($densityVolumeUnitId);
        if (is_null($volumeUnit) || is_null($volumeUnit->ml)) return null;

        return ($densityMassAmount * $massUnit->g) / ($densityVolumeAmount * $volumeUnit->ml);
    }

}
