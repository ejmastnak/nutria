<?php
namespace App\Services;

use App\Models\Ingredient;
use App\Models\IngredientNutrient;
use App\Models\Unit;
use App\Services\ComputeDensityService;
use App\Services\ConvertToGramsService;
use App\Services\AmountPer100gService;
use Illuminate\Support\Facades\DB;

class IngredientService
{
    public function storeIngredient(array $data, int $userId): ?Ingredient
    {
        $ingredient = null;
        DB::transaction(function () use ($data, $userId, &$ingredient) {

            // Create ingredient
            $ingredient = Ingredient::create([
                'name' => $data['name'],
                'fdc_id' => null,
                'ingredient_category_id' => $data['ingredient_category_id'],
                'ingredient_nutrient_amount' => $data['ingredient_nutrient_amount'],
                'ingredient_nutrient_amount_unit_id' => $data['ingredient_nutrient_amount_unit_id'],
                'density_mass_unit_id' => $data['density_mass_unit_id'],
                'density_mass_amount' => $data['density_mass_amount'],
                'density_volume_unit_id' => $data['density_volume_unit_id'],
                'density_volume_amount' => $data['density_volume_amount'],
                'density_g_ml' => ComputeDensityService::computeDensity(
                    $data['density_mass_unit_id'],
                    $data['density_mass_amount'],
                    $data['density_volume_unit_id'],
                    $data['density_volume_amount'],
                ),
                'user_id' => $userId,
            ]);

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
                    'custom_grams' => ConvertToGramsService::convertToGrams($customUnit['custom_mass_unit_id'], $customUnit['custom_mass_amount'], null, null)/$customUnit['custom_unit_amount'],
                ]);
            }

            // Create ingredient's nutrients
            foreach ($data['ingredient_nutrients'] as $ingredientNutrient) {
                IngredientNutrient::create([
                    'ingredient_id' => $ingredient->id,
                    'nutrient_id' => $ingredientNutrient['nutrient_id'],
                    'amount' => $ingredientNutrient['amount'],
                    'amount_per_100g' => AmountPer100gService::computeAmountPer100g(
                        $ingredientNutrient['amount'],
                        $data['ingredient_nutrient_amount'],
                        $data['ingredient_nutrient_amount_unit'],
                        $ingredient->density_g_ml,
                    ),
                ]);
            }

        });
        return $ingredient;
    }

    public function updateIngredient(array $data, Ingredient $ingredient): ?Ingredient
    {
        DB::transaction(function () use ($data, $ingredient) {

            // Update ingredient
            $ingredient->update([
                'name' => $data['name'],
                'ingredient_category_id' => $data['ingredient_category_id'],
                'ingredient_nutrient_amount' => $data['ingredient_nutrient_amount'],
                'ingredient_nutrient_amount_unit_id' => $data['ingredient_nutrient_amount_unit_id'],
                'density_mass_unit_id' => $data['density_mass_unit_id'],
                'density_mass_amount' => $data['density_mass_amount'],
                'density_volume_unit_id' => $data['density_volume_unit_id'],
                'density_volume_amount' => $data['density_volume_amount'],
                'density_g_ml' => ComputeDensityService::computeDensity(
                    $data['density_mass_unit_id'],
                    $data['density_mass_amount'],
                    $data['density_volume_unit_id'],
                    $data['density_volume_amount'],
                ),
            ]);

            // Update ingredient's custom units.
            $freshCustomUnitIds = [];
            $numberMassAndVolumeUnits = Unit::numberMassAndVolumeUnits();
            foreach ($ingredient->custom_units() as $idx=>$customUnit) {
                if (is_null($customUnit['id'])) {
                    $freshCustomUnitIds[] = Unit::create([
                        'name' => $customUnit['name'],
                        'seq_num' => $numberMassAndVolumeUnits + $idx,
                        'ingredient_id' => $ingredient->id,
                        'custom_unit_amount' => $customUnit['custom_unit_amount'],
                        'custom_mass_amount' => $customUnit['custom_mass_amount'],
                        'custom_mass_unit_id' => $customUnit['custom_mass_unit_id'],
                        'custom_grams' => ConvertToGramsService::convertToGrams($customUnit['custom_mass_unit_id'], $customUnit['custom_mass_amount'], null, null)/$customUnit['custom_unit_amount'],
                    ])->id;
                } else {
                    $CustomUnit = Unit::find($customUnit['id']);
                    $CustomUnit->update([
                        'name' => $customUnit['name'],
                        'seq_num' => $numberMassAndVolumeUnits + $idx,
                        'custom_unit_amount' => $customUnit['custom_unit_amount'],
                        'custom_mass_amount' => $customUnit['custom_mass_amount'],
                        'custom_mass_unit_id' => $customUnit['custom_mass_unit_id'],
                        'custom_grams' => ConvertToGramsService::convertToGrams($customUnit['custom_mass_unit_id'], $customUnit['custom_mass_amount'], null, null)/$customUnit['custom_unit_amount'],
                    ]);
                    $freshCustomUnitIds[] = $CustomUnit['id'];
                }
            }

            // Delete stale custom units
            foreach ($ingredient->custom_units() as $customUnit) {
                if (!in_array($customUnit->id, $freshCustomUnitIds)) $customUnit->delete();
            }

            // Update ingredient's nutrients
            foreach ($data['ingredient_nutrients'] as $ingredientNutrient) {
                $IngredientNutrient = IngredientNutrient::find($ingredientNutrient['id']);
                $IngredientNutrient->update([
                    'amount' => $ingredientNutrient['amount'],
                    'amount_per_100g' => AmountPer100gService::computeAmountPer100g(
                        $ingredientNutrient['amount'],
                        $data['ingredient_nutrient_amount'],
                        $data['ingredient_nutrient_amount_unit'],
                        $ingredient->density_g_ml,
                    ),
                ]);
            }


        });
        return $ingredient;
    }

    public function deleteIngredient(Ingredient $ingredient) {
        $restricted = false;
        $success = false;
        $errors = [];

        // Check for ingredient use in meals
        if ($ingredient->meal_ingredients->count() > 0) {
            $restricted = true;
            $message = "Failed to delete ingredient.";
            $errors[] = "Deleting the ingredient is intentionally restricted because the ingredient is used in one or more meals (which you can check on the ingredient's page).";
        }

        // Check for ingredient use in food lists
        if ($ingredient->food_list_ingredients->count() > 0) {
            $restricted = true;
            $message = "Failed to delete ingredient.";
            $errors[] = "Deleting the ingredient is intentionally restricted because the ingredient is used in one or more food lists (which you can check on the ingredient's page).";
        }

        if (!$restricted) $success = $ingredient->delete();

        if ($success) $message = 'Success! Ingredient deleted successfully.';
        else if (!$success && !$restricted) $message = 'Error. Failed to delete ingredient.';

        return [
            'success' => $success,
            'restricted' => $restricted,
            'message' => $message,
            'errors' => $errors,
        ];
    }

}
