<?php
namespace App\Services;

use App\Models\Meal;
use App\Models\MealIngredient;
use App\Models\IngredientCategory;
use App\Models\Ingredient;
use App\Models\IngredientNutrient;
use App\Models\Unit;
use App\Models\Nutrient;
use App\Models\FoodIntakeRecord;
use App\Services\UnitConversionService;
use Illuminate\Support\Facades\DB;

class MealService
{
    public function storeMeal(array $data, int $userId): ?int
    {
        $meal = null;
        DB::transaction(function () use ($data, $userId, &$meal) {

            $mealMassInGrams = 0;
            $meal = Meal::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'mass_in_grams' => $mealMassInGrams,
                'user_id' => $userId,
            ]);

            // Create the meal's ingredients
            foreach ($data['meal_ingredients'] as $idx=>$mealIngredient) {
                $MealIngredient = MealIngredient::create([
                    'meal_id' => $meal->id,
                    'ingredient_id' => $mealIngredient['ingredient_id'],
                    'amount' => $mealIngredient['amount'],
                    'unit_id' => $mealIngredient['unit_id'],
                    'mass_in_grams' => UnitConversionService::convertToGrams($mealIngredient['amount'], $mealIngredient['unit_id'], $mealIngredient['ingredient_id'], null, null),
                    'seq_num' => $idx,
                ]);
                $mealMassInGrams += $MealIngredient->mass_in_grams;
            }

            // Add meal's mass in grams
            $meal->update([ 'mass_in_grams' => $mealMassInGrams ]);

            // Create a meal-specific unit
            Unit::create([
                'name' => 'meal',
                'seq_num' => -1,
                'meal_id' => $meal->id,
                'custom_unit_amount' => 1,
                'custom_mass_amount' => $mealMassInGrams,
                'custom_mass_unit_id' => Unit::gramId(),
                'custom_grams' => $mealMassInGrams,
            ]);

        });
        return $meal->id;
    }

    public function updateMeal(array $data, Meal $meal, NutrientProfileService $nutrientProfileService): void
    {
        DB::transaction(function () use ($data, $meal, $nutrientProfileService) {

            $mealMassInGrams = 0;
            $freshMealIngredientIds = [];

            // Create new and update existing meal ingredients
            foreach ($data['meal_ingredients'] as $idx=>$mealIngredient) {
                if (is_null($mealIngredient['id'])) {
                    $MealIngredient = MealIngredient::create([
                        'meal_id' => $meal->id,
                        'ingredient_id' => $mealIngredient['ingredient_id'],
                        'amount' => $mealIngredient['amount'],
                        'unit_id' => $mealIngredient['unit_id'],
                        'mass_in_grams' => UnitConversionService::convertToGrams($mealIngredient['amount'], $mealIngredient['unit_id'], $mealIngredient['ingredient_id'], null, null),
                        'seq_num' => $idx,
                    ]);
                    $freshMealIngredientIds[] = $MealIngredient->id;
                    $mealMassInGrams += $MealIngredient->mass_in_grams;
                } else {
                    $MealIngredient = MealIngredient::find($mealIngredient['id']);
                    $MealIngredient->update([
                        'ingredient_id' => $mealIngredient['ingredient_id'],
                        'amount' => $mealIngredient['amount'],
                        'unit_id' => $mealIngredient['unit_id'],
                        'mass_in_grams' => UnitConversionService::convertToGrams($mealIngredient['amount'], $mealIngredient['unit_id'], $mealIngredient['ingredient_id'], null, null),
                        'seq_num' => $idx,
                    ]);
                    $freshMealIngredientIds[] = $MealIngredient->id;
                    $mealMassInGrams += $MealIngredient->mass_in_grams;
                }
            }

            // Delete stale meal ingredients
            foreach ($meal->meal_ingredients as $mealIngredient) {
                if (!in_array($mealIngredient->id, $freshMealIngredientIds)) {
                    $mealIngredient->delete();
                }
            }

            // Update meal's custom unit
            $unit = $meal->meal_unit;
            if (!is_null($unit)) {
                $unit->update([
                    'custom_mass_amount' => $mealMassInGrams,
                    'custom_grams' => $mealMassInGrams,
                ]);
            }

            // Update meal after MealIngredients, to allow calculating mass
            $meal->update([
                'name' => $data['name'],
                'description' => $data['description'],
                'mass_in_grams' => $mealMassInGrams,
            ]);

            // Update potential child ingredient
            $childIngredient = Ingredient::where('meal_id', $meal->id)->first();
            if (!is_null($childIngredient)) $this->updateChildIngredient($meal, $childIngredient, $nutrientProfileService);

        });
    }

    public function saveAsIngredient(Meal $meal, int $userId, NutrientProfileService $nutrientProfileService): ?int {

        // Does a child ingredient for this meal already exist?
        $childIngredient = Ingredient::where('meal_id', $meal->id)->first();
        if (!is_null($childIngredient)) return $this->updateChildIngredient($meal, $childIngredient, $nutrientProfileService);

        $ingredient = null;
        DB::transaction(function () use ($meal, $userId, &$ingredient, $nutrientProfileService) {

            // Create ingredient
            $ingredient = Ingredient::create([
                'name' => $meal->name . ' (ingredient)',
                'description' => $meal->description,
                'ingredient_category_id' => IngredientCategory::otherCategory()->id,
                'nutrient_content_unit_amount' => 100,
                'nutrient_content_unit_id' => Unit::gramId(),
                'meal_id' => $meal->id,
                'user_id' => $userId,
            ]);

            // Create ingredient's nutrients
            $nutrientProfile = $nutrientProfileService->getNutrientIndexedMealProfile($meal->id);
            foreach (Nutrient::all() as $nutrient) {
                IngredientNutrient::create([
                    'ingredient_id' => $ingredient->id,
                    'nutrient_id' => $nutrient->id,
                    'amount' => $nutrientProfile[$nutrient->id] ? $nutrientProfile[$nutrient->id]->amount * (100/$meal->mass_in_grams) : 0.0,
                    'amount_per_100g' => $nutrientProfile[$nutrient->id] ? $nutrientProfile[$nutrient->id]->amount * (100/$meal->mass_in_grams) : 0.0,
                ]);
            }
        });
        return $ingredient->id;
    }

    public function updateChildIngredient(Meal $meal, Ingredient $ingredient, NutrientProfileService $nutrientProfileService): ?int
    {
        DB::transaction(function () use ($meal, $ingredient, $nutrientProfileService) {

            // Update ingredient
            $ingredient->update([
                'name' => $meal->name . ' (ingredient)',
                'description' => $meal->description,
            ]);

            // Update ingredient's nutrients
            $nutrientProfile = $nutrientProfileService->getNutrientIndexedMealProfile($meal->id);
            foreach ($ingredient->ingredient_nutrients as $ingredientNutrient) {
                $ingredientNutrient->update([
                    'amount' => $nutrientProfile[$ingredientNutrient->nutrient_id] ? $nutrientProfile[$ingredientNutrient->nutrient_id]->amount * (100/$meal->mass_in_grams) : 0.0,
                    'amount_per_100g' => $nutrientProfile[$ingredientNutrient->nutrient_id] ? $nutrientProfile[$ingredientNutrient->nutrient_id]->amount * (100/$meal->mass_in_grams) : 0.0,
                ]);
            }
        });
        return $ingredient->id;
    }

    public function storeAndLogMeal(array $data, int $userId): ?int
    {
        $meal = null;
        DB::transaction(function () use ($data, $userId, &$meal) {

            $mealMassInGrams = 0;
            $meal = Meal::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'mass_in_grams' => $mealMassInGrams,
                'user_id' => $userId,
            ]);

            // Create the meal's ingredients
            foreach ($data['meal_ingredients'] as $idx=>$mealIngredient) {
                $MealIngredient = MealIngredient::create([
                    'meal_id' => $meal->id,
                    'ingredient_id' => $mealIngredient['ingredient_id'],
                    'amount' => $mealIngredient['amount'],
                    'unit_id' => $mealIngredient['unit_id'],
                    'mass_in_grams' => UnitConversionService::convertToGrams($mealIngredient['amount'], $mealIngredient['unit_id'], $mealIngredient['ingredient_id'], null, null),
                    'seq_num' => $idx,
                ]);
                $mealMassInGrams += $MealIngredient->mass_in_grams;
            }

            // Add meal's mass in grams
            $meal->update([ 'mass_in_grams' => $mealMassInGrams ]);

            // Create a meal-specific unit
            $mealUnit = Unit::create([
                'name' => 'meal',
                'seq_num' => -1,
                'meal_id' => $meal->id,
                'custom_unit_amount' => 1,
                'custom_mass_amount' => $mealMassInGrams,
                'custom_mass_unit_id' => Unit::gramId(),
                'custom_grams' => $mealMassInGrams,
            ]);

            // Create a FoodIntakeRecord to log meal intake
            $amount = 1.0;
            FoodIntakeRecord::create([
                'ingredient_id' => null,
                'meal_id' => $meal->id,
                'amount' => $amount,
                'unit_id' => $mealUnit->id,
                'mass_in_grams' => UnitConversionService::convertToGrams($amount, $mealUnit->id, null, $meal->id, null),
                'date_time_utc' => $data['date_time_utc'],
                'user_id' => $userId,
            ]);

        });
        return $meal->id;
    }

    public function deleteMeal(Meal $meal): array
    {
        $restricted = false;
        $success = false;
        $errors = [];

        // Check for meal use in food lists
        if ($meal->food_list_meals->count() > 0) {
            $restricted = true;
            $message = "Failed to delete meal.";
            $errors[] = "Deleting the meal is intentionally restricted because the meal is used in one or more food lists (which you can check on the meals's page).";
        }

        // Check for meal use in food intake records
        if ($meal->food_intake_records->count() > 0) {
            $restricted = true;
            $message = "Failed to delete meal.";
            $errors[] = "Deleting the meal is intentionally restricted because the meal is used in one or more food intake records.";
        }

        // Check if the meal has a child ingredient used in meals or food lists
        $ingredient = $meal->ingredient;
        if (!is_null($ingredient)) {
            // Check for ingredient use in meals or food lists
            if ($ingredient->meal_ingredients->count() > 0 || $ingredient->food_list_ingredients->count() > 0) {
                $restricted = true;
                $message = "Failed to delete meal.";
                $errors[] = "Deleting the meal is intentionally restricted because the meal has a derived ingredient used in one or more meals or food lists (which you can check on the derived ingredient's page).";
            }
        }

        if (!$restricted) $success = $meal->delete();

        if ($success) $message = 'Success! Meal deleted successfully.';
        else if (!$success && !$restricted) $message = 'Error. Failed to delete ingredient.';

        return [
            'success' => $success,
            'restricted' => $restricted,
            'message' => $message,
            'errors' => $errors,
        ];
    }

}
