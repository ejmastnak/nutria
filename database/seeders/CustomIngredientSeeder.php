<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\Models\Unit;
use App\Models\IngredientCategory;
use App\Models\Nutrient;
use App\Models\Ingredient;
use App\Models\IngredientNutrient;
use App\Services\ComputeDensityService;
use App\Services\ConvertToGramsService;
use App\Services\AmountPer100gService;
use Illuminate\Support\Facades\DB;

class CustomIngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $json = Storage::disk('seeders')->get('json/ingredients.json');
        $ingredients = json_decode($json, true);

        $numberMassAndVolumeUnits = Unit::numberMassAndVolumeUnits();

        foreach ($ingredients as $ingredient) {

            DB::beginTransaction();

            $ingredientCategoryId = null;
            if (!is_null($ingredient['ingredient_category'])) {
                $ingredientCategory = IngredientCategory::where('name', $ingredient['ingredient_category'])->first();
                if (is_null($ingredientCategory)) {
                    $this->command->info("Warning: did not find ingredient category with name " . $ingredient['ingredient_category'] . " when seeding custom Ingredients.");
                    DB::rollBack();
                    continue;
                }
                $ingredientCategoryId = $ingredientCategory->id;
            }

            $ingredientNutrientAmountUnit = Unit::where('name', $ingredient['ingredient_nutrient_amount_unit'])->first();
            if (is_null($ingredientNutrientAmountUnit)) {
                $this->command->info("Warning: did not find unit with name " . $ingredient['ingredient_nutrient_amount_unit'] . " when seeding custom Ingredients.");
                DB::rollBack();
                continue;
            }

            $densityMassUnitId = null;
            if (!is_null($ingredient['density_mass_unit'])) {
                $densityMassUnit = Unit::where('name', $ingredient['density_mass_unit'])->first();
                if (is_null($densityMassUnit)) {
                    $this->command->info("Warning: did not find unit with name " . $ingredient['density_mass_unit'] . " when seeding custom Ingredients.");
                    DB::rollBack();
                    continue;
                }
                $densityMassUnitId = $densityMassUnit->id;
            }

            $densityVolumeUnitId = null;
            if (!is_null($ingredient['density_volume_unit'])) {
                $densityVolumeUnit = Unit::where('name', $ingredient['density_volume_unit'])->first();
                if (is_null($densityVolumeUnit)) {
                    $this->command->info("Warning: did not find unit with name " . $ingredient['density_volume_unit'] . " when seeding custom Ingredients.");
                    DB::rollBack();
                    continue;
                }
                $densityVolumeUnitId = $densityVolumeUnit->id;
            }

            $densityGMl = ComputeDensityService::computeDensity(
                $densityMassUnit->id,
                $ingredient['density_mass_amount'],
                $densityVolumeUnit->id,
                $ingredient['density_volume_amount'],
            );
            if (is_null($densityGMl) && (!is_null($ingredient['density_mass_amount']) || !is_null($ingredient['density_volume_amount']))) {
                $this->command->info("Warning: failed to compute density when seeding custom ingredient with name " . $ingredient['name'] . ".");
                DB::rollBack();
                continue;
            }

            $Ingredient = Ingredient::updateOrCreate([
                'name' => $ingredient['name'],
                'ingredient_category_id' => $ingredientCategoryId,
                'user_id' => $ingredient['user_id'],
            ], [
                    'ingredient_nutrient_amount' => $ingredient['ingredient_nutrient_amount'],
                    'ingredient_nutrient_amount_unit_id' => $ingredientNutrientAmountUnit->id,
                    'density_mass_unit_id' => $densityMassUnitId,
                    'density_mass_amount' => $ingredient['density_mass_amount'],
                    'density_volume_unit_id' => $densityVolumeUnitId,
                    'density_volume_amount' => $ingredient['density_volume_amount'],
                    'density_g_ml' => $densityGMl,
                ]);

            // Create ingredient-specific units
            foreach ($ingredient['custom_units'] as $idx=>$customUnit) {

                $customMassUnit = Unit::where('name', $customUnit['custom_mass_unit'])->first();
                if (is_null($customMassUnit)) {
                    $this->command->info("Warning: did not find unit with name " . $customUnit['customMassUnit'] . " when seeding custom units for custom ingredient " . $ingredient['name'] . ".");
                    continue;
                }

                Unit::updateOrCreate([
                    'name' => $customUnit['name'],
                    'seq_num' => $numberMassAndVolumeUnits + $idx,
                    'ingredient_id' => $Ingredient->id,
                    'custom_unit_amount' => $customUnit['custom_unit_amount'],
                    'custom_mass_amount' => $customUnit['custom_mass_amount'],
                    'custom_mass_unit_id' => $customMassUnit->id,
                    'custom_grams' => ConvertToGramsService::convertToGrams($customMassUnit->id, $customUnit['custom_mass_amount'], null, null)/$customUnit['custom_unit_amount'],
                ]);
            }

            /**
             *  Map ingredient nutrients to nutrient_id-indexed symbol table
             *  for easier nutrient lookup
             */
            $ingredientNutrients = [];
            foreach($ingredient['ingredient_nutrients'] as $ign) $ingredientNutrients[$ign['nutrient_id']] = $ign;

            foreach (Nutrient::all() as $nutrient) {
                if (array_key_exists($nutrient->id, $ingredientNutrients)) {

                    $amountPer100g = AmountPer100gService::computeAmountPer100g(
                        $ingredientNutrients[$nutrient->id]['amount'],
                        $ingredient['ingredient_nutrient_amount'],
                        $ingredientNutrientAmountUnit->id,
                        $densityGMl,
                    );
                    if (is_null($amountPer100g)) {
                        $this->command->info("Warning: failed to compute nutrient amount per 100 grams for nutrient " . $nutrient->name . " when seeding custom ingredient " . $ingredient['name'] . ".");
                        continue;
                    }
                    IngredientNutrient::updateOrCreate([
                        'ingredient_id' => $Ingredient->id,
                        'nutrient_id' => $nutrient->id,
                        'amount' => $ingredientNutrients[$nutrient->id]['amount'],
                        'amount_per_100g' => $amountPer100g,
                    ]);
                } else {
                    IngredientNutrient::updateOrCreate([
                        'ingredient_id' => $Ingredient->id,
                        'nutrient_id' => $nutrient->id,
                        'amount' => 0,
                        'amount_per_100g' => 0,
                    ]);
                }
            }
            DB::commit();

        }

    }
}
