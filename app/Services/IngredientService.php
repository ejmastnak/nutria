<?php
namespace App\Services;

use App\Models\Ingredient;
use App\Models\IngredientNutrient;
use App\Models\Unit;
use App\Services\ComputeDensityService;
use App\Services\ConvertToGramsService;
use Illuminate\Support\Facades\DB;

class IngredientService
{
    public function storeIngredient(array $data, int $userId, ComputeDensityService $computeDensityService, ConvertToGramsService $convertToGramsService): ?Ingredient
    {
        $ingredient = null;
        DB::transaction(function () use ($data, $userId, &$ingredient, $computeDensityService, $convertToGramsService) {

            // Create ingredient
            $ingredient = Ingredient::create([
                'name' => $data['name'],
                'fdc_id' => null,
                'ingredient_category_id' => $data['ingredient_category_id'],
                'density_mass_unit_id' => $data['density_mass_unit_id'],
                'density_mass_amount' => $data['density_mass_amount'],
                'density_volume_unit_id' => $data['density_volume_unit_id'],
                'density_volume_amount' => $data['density_volume_amount'],
                'density_g_ml' => $computeDensityService->computeDensity(
                    $data['density_mass_unit_id'],
                    $data['density_mass_amount'],
                    $data['density_volume_unit_id'],
                    $data['density_volume_amount'],
                ),
                'user_id' => $userId,
            ]);

            // Create ingredient's nutrients
            foreach ($data['ingredient_nutrients'] as $ingredientNutrient) {
                IngredientNutrient::create([
                    'ingredient_id' => $ingredient->id,
                    'nutrient_id' => $ingredientNutrient['nutrient_id'],
                    'amount_per_100g' => $ingredientNutrient['amount_per_100g'],
                ]);
            }

            // Create ingredient-specific custom units, if applicable
            $numberMassAndVolumeUnits = Unit::numberMassAndVolumeUnits();
            foreach ($data['custom_units'] as $idx=>$customUnit) {
                Unit::create([
                    'name' => $customUnit['name'],
                    'seq_num' => $numberMassAndVolumeUnits + $idx,
                    'ingredient_id' => $ingredient->id,
                    'custom_unit_amount' => $customUnit['custom_unit_amount'],
                    'custom_mass_amount' => $customUnit['custom_mass_amount'],
                    'custom_mass_unit_id' => $customUnit['custom_mass_unit_id'],
                    'custom_grams' => $convertToGramsService->convertToGrams($customUnit['custom_mass_unit_id'], $customUnit['custom_mass_amount'], null, null)/$customUnit['custom_unit_amount'],
                ]);
            }

        });
        return $ingredient;
    }

    public function updateIngredient(array $data, Ingredient $ingredient, ComputeDensityService $computeDensityService, ConvertToGramsService $convertToGramsService): ?Ingredient
    {
        DB::transaction(function () use ($data, $ingredient, $computeDensityService, $convertToGramsService) {

            // Update ingredient
            $ingredient->update([
                'name' => $data['name'],
                'ingredient_category_id' => $data['ingredient_category_id'],
                'density_mass_unit_id' => $data['density_mass_unit_id'],
                'density_mass_amount' => $data['density_mass_amount'],
                'density_volume_unit_id' => $data['density_volume_unit_id'],
                'density_volume_amount' => $data['density_volume_amount'],
                'density_g_ml' => $computeDensityService->computeDensity(
                    $data['density_mass_unit_id'],
                    $data['density_mass_amount'],
                    $data['density_volume_unit_id'],
                    $data['density_volume_amount'],
                ),
            ]);

            // Update ingredient's nutrients
            foreach ($data['ingredient_nutrients'] as $ingredientNutrient) {
                $IngredientNutrient = IngredientNutrient::find($ingredientNutrient['id']);
                $IngredientNutrient->update([
                    'amount_per_100g' => $ingredientNutrient['amount_per_100g'],
                ]);
            }

            // Refresh ingredient's custom units. I'm just deleting all and
            // recreating; it's not worth the complexity of using the
            // create-new-update-existing-delete-stale protocol.
            foreach ($ingredient->customUnits() as $customUnit) $customUnit->delete();
            $numberMassAndVolumeUnits = Unit::numberMassAndVolumeUnits();
            foreach ($data['custom_units'] as $idx=>$customUnit) {
                Unit::create([
                    'name' => $customUnit['name'],
                    'seq_num' => $numberMassAndVolumeUnits + $idx,
                    'ingredient_id' => $ingredient->id,
                    'custom_unit_amount' => $customUnit['custom_unit_amount'],
                    'custom_mass_amount' => $customUnit['custom_mass_amount'],
                    'custom_mass_unit_id' => $customUnit['custom_mass_unit_id'],
                    'custom_grams' => $convertToGramsService->convertToGrams($customUnit['custom_mass_unit_id'], $customUnit['custom_mass_amount'], null, null)/$customUnit['custom_unit_amount'],
                ]);
            }

        });
        return $ingredient;
    }

}
