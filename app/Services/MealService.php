<?php
namespace App\Services;

use App\Models\Meal;
use App\Models\MealIngredient;
use App\Models\Unit;
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
                    'mass_in_grams' => $convertToGramsService->convertToGrams($mealIngredient['amount'], $mealIngredient['unit_id'], $mealIngredient['ingredient_id']),
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
                        'mass_in_grams' => $convertToGramsService->convertToGrams($mealIngredient['amount'], $mealIngredient['unit_id'], $mealIngredient['ingredient_id']),
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
                        'mass_in_grams' => $convertToGramsService->convertToGrams($mealIngredient['amount'], $mealIngredient['unit_id'], $mealIngredient['ingredient_id']),
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

        });
        return $meal;
    }

}
