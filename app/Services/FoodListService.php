<?php
namespace App\Services;

use App\Models\FoodList;
use App\Models\FoodListIngredient;
use App\Models\FoodListMeal;
use App\Models\Unit;
use App\Services\ConvertToGramsService;
use Illuminate\Support\Facades\DB;

class FoodListService
{
    public function storeFoodList(array $data, int $userId, ConvertToGramsService $convertToGramsService): ?FoodList
    {
        $foodList = null;
        DB::transaction(function () use ($data, $userId, &$foodList, $convertToGramsService) {

            $foodListMassInGrams = 0;
            $foodList = FoodList::create([
                'name' => $data['name'],
                'mass_in_grams' => $foodListMassInGrams,
                'user_id' => $userId,
            ]);

            // Create FoodListIngredients
            foreach (data['food_list_ingredients'] as $idx=>$fli) {
                $fli = FoodListIngredient::create([
                    'food_list_id' => $foodList->id,
                    'ingredient_id' => $fli['ingredient_id'],
                    'amount' => $fli['amount'],
                    'unit_id' => $fli['unit_id'],
                    'mass_in_grams' => $convertToGramsService->convertToGrams($fli['amount'], $fli['unit_id'], $fli['ingredient_id'], null),
                    'seq_num' => $idx,
                ]);
                $foodListMassInGrams += $fli->mass_in_grams;
            }

            // Create FoodListMeal
            foreach (data['food_list_meals'] as $idx=>$flm) {
                $flm = FoodListMeal::create([
                    'food_list_id' => $foodList->id,
                    'meal_id' => $flm['meal_id'],
                    'amount' => $flm['amount'],
                    'unit_id' => $flm['unit_id'],
                    'mass_in_grams' => $convertToGramsService->convertToGrams($flm['amount'], $flm['unit_id'], null, $flm['meal_id']),
                    'seq_num' => $idx,
                ]);
                $foodListMassInGrams += $flm->mass_in_grams;
            }

            // Add food list's mass in grams
            $foodList->update([ 'mass_in_grams' => $foodListMassInGrams ]);

        });
        return $foodList;
    }

    public function updateFoodList(array $data, FoodList $foodList, ConvertToGramsService $convertToGramsService): ?FoodList
    {
        DB::transaction(function () use ($data, $foodList, $convertToGramsService) {

            $foodListMassInGrams = 0;
            $freshFoodListIngredientIds = [];
            $freshFoodListMealIds = [];

            // ------------------------------------------------------------- //
            // Process food list ingredients
            // ------------------------------------------------------------- //
            // Create new and update existing food list ingredients
            foreach ($data['food_list_ingredients'] as $idx=>$fli) {
                if (is_null($fli['id'])) {
                    $FoodListIngredient = FoodListIngredient::create([
                        'food_list_id' => $foodList->id,
                        'ingredient_id' => $fli['ingredient_id'],
                        'amount' => $fli['amount'],
                        'unit_id' => $fli['unit_id'],
                        'mass_in_grams' => $convertToGramsService->convertToGrams($fli['amount'], $fli['unit_id'], $fli['ingredient_id'], null),
                        'seq_num' => $idx,
                    ]);
                    $freshFoodListIngredientIds[] = $FoodListIngredient->id;
                    $foodListMassInGrams += $FoodListIngredient->mass_in_grams;
                } else {
                    $FoodListIngredient = FoodListIngredient::find($fli['id']);
                    $FoodListIngredient->update([
                        'ingredient_id' => $fli['ingredient_id'],
                        'amount' => $fli['amount'],
                        'unit_id' => $fli['unit_id'],
                        'mass_in_grams' => $convertToGramsService->convertToGrams($fli['amount'], $fli['unit_id'], $fli['ingredient_id'], null),
                        'seq_num' => $idx,
                    ]);
                    $freshFoodListIngredientIds[] = $FoodListIngredient->id;
                    $foodListMassInGrams += $FoodListIngredient->mass_in_grams;
                }
            }

            // Delete stale food list ingredients
            foreach ($foodList->foodListIngredients as $fli) {
                if (!in_array($fli->id, $freshFoodListIngredientIds)) $fli->delete();
            }
            // ------------------------------------------------------------- //

            // ------------------------------------------------------------- //
            // Process food list meals
            // ------------------------------------------------------------- //
            // Create new and update existing food list meals
            foreach ($data['food_list_meals'] as $idx=>$flm) {
                if (is_null($flm['id'])) {
                    $FoodListMeal = FoodListMeal::create([
                        'food_list_id' => $foodList->id,
                        'meal_id' => $flm['meal_id'],
                        'amount' => $flm['amount'],
                        'unit_id' => $flm['unit_id'],
                        'mass_in_grams' => $convertToGramsService->convertToGrams($flm['amount'], $flm['unit_id'], null, $flm['meal_id']),
                        'seq_num' => $idx,
                    ]);
                    $freshFoodListMealIds[] = $FoodListMeal->id;
                    $foodListMassInGrams += $FoodListMeal->mass_in_grams;
                } else {
                    $FoodListMeal = FoodListMeal::find($flm['id']);
                    $FoodListMeal->update([
                        'meal_id' => $flm['meal_id'],
                        'amount' => $flm['amount'],
                        'unit_id' => $flm['unit_id'],
                        'mass_in_grams' => $convertToGramsService->convertToGrams($flm['amount'], $flm['unit_id'], null, $flm['meal_id']),
                        'seq_num' => $idx,
                    ]);
                    $freshFoodListMealIds[] = $FoodListMeal->id;
                    $foodListMassInGrams += $FoodListMeal->mass_in_grams;
                }
            }

            // Delete stale food list meals
            foreach ($foodList->foodListMeals as $flm) {
                if (!in_array($flm->id, $freshFoodListMealIds)) $flm->delete();
            }
            // ------------------------------------------------------------- //

            // Update food list after meals and ingredients, to allow
            // calculating food list mass
            $foodList->update([
                'name' => $data['name'],
                'mass_in_grams' => $foodListMassInGrams,
            ]);

        });
        return $foodList;
    }

}
