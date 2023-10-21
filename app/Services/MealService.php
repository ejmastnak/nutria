<?php
namespace App\Services;

use App\Models\Meal;
use App\Models\MealIngredient;
use App\Models\IngredientCategory;
use App\Models\Ingredient;
use App\Models\IngredientNutrient;
use App\Models\Unit;
use App\Models\Nutrient;
use App\Services\ConvertToGramsService;
use Illuminate\Support\Facades\DB;

class MealService
{
    public function storeMeal(array $data, int $userId, ConvertToGramsService $convertToGramsService): ?Meal
    {
        $meal = null;
        DB::transaction(function () use ($data, $userId, &$meal, $convertToGramsService) {

            $mealMassInGrams = 0;
            $meal = Meal::create([
                'name' => $data['name'],
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
                    'mass_in_grams' => $convertToGramsService->convertToGrams($mealIngredient['amount'], $mealIngredient['unit_id'], $mealIngredient['ingredient_id'], null),
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
        return $meal;
    }

    public function updateMeal(array $data, Meal $meal, ConvertToGramsService $convertToGramsService): ?Meal
    {
        DB::transaction(function () use ($data, $meal, $convertToGramsService) {

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
                        'mass_in_grams' => $convertToGramsService->convertToGrams($mealIngredient['amount'], $mealIngredient['unit_id'], $mealIngredient['ingredient_id'], null),
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
                        'mass_in_grams' => $convertToGramsService->convertToGrams($mealIngredient['amount'], $mealIngredient['unit_id'], $mealIngredient['ingredient_id'], null),
                        'seq_num' => $idx,
                    ]);
                    $freshMealIngredientIds[] = $MealIngredient->id;
                    $mealMassInGrams += $MealIngredient->mass_in_grams;
                }
            }

            // Delete stale meal ingredients
            foreach ($meal->mealIngredients as $mealIngredient) {
                if (!in_array($mealIngredient->id, $freshMealIngredientIds)) {
                    $mealIngredient->delete();
                }
            }

            // Update meal's custom unit
            $unit = Unit::where('meal_id', $meal->id)->first();
            if (!is_null($unit)) {
                $unit->update([
                    'custom_mass_amount' => $mealMassInGrams,
                    'custom_grams' => $mealMassInGrams,
                ]);
            }

            // Update meal after MealIngredients, to allow calculating mass
            $meal->update([
                'name' => $data['name'],
                'mass_in_grams' => $mealMassInGrams,
            ]);

            // Update potential child ingredient
            $childIngredient = Ingredient::where('meal_id', $meal->id)->first();
            if (!is_null($childIngredient)) $this->updateChildIngredient($meal, $childIngredient);

        });
        return $meal;
    }

    public function saveAsIngredient(Meal $meal, int $userId, NutrientProfileService $nutrientProfileService): ?Ingredient {

        // Does a child ingredient for this meal already exist?
        $childIngredient = Ingredient::where('meal_id', $meal->id)->first();
        if (!is_null($childIngredient)) return $this->updateChildIngredient($meal, $childIngredient);

        $ingredient = null;
        DB::transaction(function () use ($meal, $userId, &$ingredient, $nutrientProfileService) {

            // Create ingredient
            $ingredient = Ingredient::create([
                'name' => $meal->name . ' (ingredient)',
                'ingredient_category_id' => IngredientCategory::otherCategory()->id,
                'meal_id' => $meal->id,
                'user_id' => $userId,
            ]);

            // Create ingredient's nutrients
            $nutrientProfile = $nutrientProfileService->getNutrientIndexedMealProfile($meal->id);
            foreach ($nutrient as Nutrient::all()) {
                IngredientNutrient::create([
                    'ingredient_id' => $ingredient->id,
                    'nutrient_id' => $nutrient->id,
                    'amount_per_100g' => $nutrientProfile[$nutrient->id] ? $nutrientProfile[$nutrient->id]->amount * (100/$meal->mass_in_grams) : 0.0,
                ]);
            }
        });
        return $ingredient;
    }

    public function updateChildIngredient(Meal $meal, Ingredient $ingredient): ?Ingredient {
        DB::transaction(function () use ($meal, $userId, $ingredient, $nutrientProfileService) {

            // Update ingredient
            $ingredient = Ingredient::create([ 'name' => $meal->name . ' (ingredient)' ]);

            // Update ingredient's nutrients
            $nutrientProfile = $nutrientProfileService->getNutrientIndexedMealProfile($meal->id);
            foreach ($ingredientNutrient as $ingredient->ingredientNutrients) {
                $ingredientNutrient->update([
                    'amount_per_100g' => $nutrientProfile[$ingredientNutrient->nutrient_id] ? $nutrientProfile[$ingredientNutrient->nutrient_id]->amount * (100/$meal->mass_in_grams) : 0.0,
                ]);
            }
        });
        return $ingredient;
    }

    public function deleteMeal(Meal $meal) {
        $restricted = false;
        $success = false;
        $errors = [];

        // Check for meal use in food lists
        if ($meal->foodListMeals->count() > 0) {
            $restricted = true;
            $message = "Failed to delete meal.";
            $errors[] = "Deleting the meal is intentionally restricted because the meal is used in one or more food lists (which you can check on the meals's page).";
        }

        // Check if the meal has a child ingredient used in meals or food lists
        $ingredient = $meal->ingredient;
        if (!is_null($ingredient)) {
            // Check for ingredient use in meals or food lists
            if ($ingredient->mealIngredients->count() > 0 || $ingredient->foodListIngredients->count() > 0) {
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