<?php
namespace App\Services;

use App\Models\Ingredient;
use App\Models\IngredientNutrient;
use App\Models\Unit;
use App\Services\ComputeDensityService;
use App\Services\UnitConversionService;
use App\Services\AmountPer100gService;
use Illuminate\Support\Facades\DB;

class IngredientService
{
    public function storeIngredient(array $data, int $userId): ?Ingredient
    {
        $ingredient = null;
        DB::transaction(function () use ($data, $userId, &$ingredient) {

            $nutrientUnitIsNewCustomUnit = !isset($data['nutrient_content_unit']['id']);

            // Create ingredient
            $ingredient = Ingredient::create([
                'fdc_id' => null,
                'name' => $data['name'],
                'ingredient_category_id' => $data['ingredient_category_id'],
                'nutrient_content_unit_amount' => $data['nutrient_content_unit_amount'],
                // If set to a to-be-created custom unit, set to grams for now
                // (we need to use a non-null value because of foreign key
                // constraints) and update later after creating unit.
                'nutrient_content_unit_id' => $nutrientUnitIsNewCustomUnit ? Unit::gramId() : $data['nutrient_content_unit']['id'],
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
                    'custom_grams' => UnitConversionService::convertToGrams($customUnit['custom_mass_amount'], $customUnit['custom_mass_unit_id'], null, null, null)/$customUnit['custom_unit_amount'],
                ]);
            }

            // For newly-created custom units
            if ($nutrientUnitIsNewCustomUnit) {
                // (I'm not checking if $unit is null because such a unit is
                // guaranteed to exist by validation, and if it does not exist,
                // the resulting error when accessing the nonexistent unit's id
                // is appropriate and cancels the transaction.)
                $unit = Unit::where([
                    ['name', 'like', $data['nutrient_content_unit']['name']],
                    ['custom_unit_amount', '=', $data['nutrient_content_unit']['custom_unit_amount']],
                    ['custom_mass_amount', '=', $data['nutrient_content_unit']['custom_mass_amount']],
                    ['custom_mass_unit_id', '=', $data['nutrient_content_unit']['custom_mass_unit_id']],
                ])->first();
                $ingredient->update([
                    'nutrient_content_unit_id' => $unit->id,
                ]);
            }

            // Create ingredient's nutrients
            foreach ($data['ingredient_nutrients'] as $ingredientNutrient) {
                IngredientNutrient::create([
                    'ingredient_id' => $ingredient->id,
                    'nutrient_id' => $ingredientNutrient['nutrient_id'],
                    'amount' => is_null($ingredientNutrient['amount']) ? 0.0 : $ingredientNutrient['amount'],
                    'amount_per_100g' => AmountPer100gService::computeAmountPer100g(
                        is_null($ingredientNutrient['amount']) ? 0.0 : $ingredientNutrient['amount'],
                        $data['nutrient_content_unit_amount'],
                        $ingredient->nutrient_content_unit_id,
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

            $nutrientUnitIsNewCustomUnit = !isset($data['nutrient_content_unit']['id']);

            // Update ingredient
            $ingredient->update([
                'name' => $data['name'],
                'ingredient_category_id' => $data['ingredient_category_id'],
                'nutrient_content_unit_amount' => $data['nutrient_content_unit_amount'],
                // If set to a to-be-created custom unit, set to grams for now
                // (we need to use a non-null value because of foreign key
                // constraints) and update later after creating unit.
                'nutrient_content_unit_id' => $nutrientUnitIsNewCustomUnit ? Unit::gramId() : $data['nutrient_content_unit']['id'],
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
            foreach ($data['custom_units'] as $idx=>$customUnit) {
                if (is_null($customUnit['id'])) {
                    $freshCustomUnitIds[] = Unit::create([
                        'name' => $customUnit['name'],
                        'seq_num' => $numberMassAndVolumeUnits + $idx,
                        'ingredient_id' => $ingredient->id,
                        'custom_unit_amount' => $customUnit['custom_unit_amount'],
                        'custom_mass_amount' => $customUnit['custom_mass_amount'],
                        'custom_mass_unit_id' => $customUnit['custom_mass_unit_id'],
                        'custom_grams' => UnitConversionService::convertToGrams($customUnit['custom_mass_amount'], $customUnit['custom_mass_unit_id'], null, null, null)/$customUnit['custom_unit_amount'],
                    ])->id;
                } else {
                    $CustomUnit = Unit::find($customUnit['id']);
                    $CustomUnit->update([
                        'name' => $customUnit['name'],
                        'seq_num' => $numberMassAndVolumeUnits + $idx,
                        'custom_unit_amount' => $customUnit['custom_unit_amount'],
                        'custom_mass_amount' => $customUnit['custom_mass_amount'],
                        'custom_mass_unit_id' => $customUnit['custom_mass_unit_id'],
                        'custom_grams' => UnitConversionService::convertToGrams($customUnit['custom_mass_amount'], $customUnit['custom_mass_unit_id'], null, null, null)/$customUnit['custom_unit_amount'],
                    ]);
                    $freshCustomUnitIds[] = $CustomUnit['id'];
                }
            }

            // Delete stale custom units
            foreach ($ingredient->custom_units as $customUnit) {
                if (!in_array($customUnit->id, $freshCustomUnitIds)) $customUnit->delete();
            }

            // For newly-created custom units
            if ($nutrientUnitIsNewCustomUnit) {
                // (I'm not checking if $unit is null because such a unit is
                // guaranteed to exist by validation, and if it does not exist,
                // the resulting error when accessing the nonexistent unit's id
                // is appropriate and cancels the transaction.)
                $unit = Unit::where([
                    ['name', 'like', $data['nutrient_content_unit']['name']],
                    ['custom_unit_amount', '=', $data['nutrient_content_unit']['custom_unit_amount']],
                    ['custom_mass_amount', '=', $data['nutrient_content_unit']['custom_mass_amount']],
                    ['custom_mass_unit_id', '=', $data['nutrient_content_unit']['custom_mass_unit_id']],
                ])->first();
                $ingredient->update([
                    'nutrient_content_unit_id' => $unit->id,
                ]);
            }

            // Update ingredient's nutrients
            foreach ($data['ingredient_nutrients'] as $ingredientNutrient) {
                $IngredientNutrient = IngredientNutrient::find($ingredientNutrient['id']);
                $IngredientNutrient->update([
                    'amount' => is_null($ingredientNutrient['amount']) ? 0.0 : $ingredientNutrient['amount'],
                    'amount_per_100g' => AmountPer100gService::computeAmountPer100g(
                        is_null($ingredientNutrient['amount']) ? 0.0 : $ingredientNutrient['amount'],
                        $data['nutrient_content_unit_amount'],
                        $ingredient->nutrient_content_unit_id,
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
