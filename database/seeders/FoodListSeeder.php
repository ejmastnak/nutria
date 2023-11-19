<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\Models\Unit;
use App\Models\FoodList;
use App\Models\FoodListIngredient;
use App\Models\FoodListMeal;
use App\Models\Ingredient;
use App\Models\Meal;

class FoodListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gramId = Unit::gramId();

        $json = Storage::disk('seeders')->get('json/food-lists.json');
        $foodLists = json_decode($json, true);

        foreach ($foodLists as $foodList) {

            $foodListMassInGrams = 0;
            $FoodList = FoodList::updateOrCreate(
                [
                    'name' => $foodList['name'],
                    'user_id' => $foodList['user_id'],
                ],
                [ 'mass_in_grams' => $foodListMassInGrams ]
            );

            foreach($foodList['food_list_ingredients'] as $idx=>$fli) {

                $ingredient = Ingredient::where('name', $fli['name'])->first();
                if (is_null($ingredient)) {
                    $this->command->info("Warning. Failed to find ingredient " . $fli['name'] . " when seeding FoodLists.");
                    continue;
                }

                FoodListIngredient::updateOrCreate([
                    'food_list_id' => $FoodList->id,
                    'ingredient_id' => $ingredient->id,
                    'amount' => $fli['mass_in_grams'],
                    'unit_id' => $gramId,
                    'mass_in_grams' => $fli['mass_in_grams'],
                    'seq_num' => $idx,
                ]);
                $foodListMassInGrams += $fli['mass_in_grams'];
            }

            foreach($foodList['food_list_meals'] as $idx=>$flm) {

                $meal = Meal::where('name', $flm['name'])->where('user_id', $foodList['user_id'])->first();
                if (is_null($meal)) {
                    $this->command->info("Warning. Failed to find meal with name " . $flm['name'] . " and user id " . $foodList['user_id'] . " when seeding FoodLists.");
                    continue;
                }

                FoodListMeal::updateOrCreate([
                    'food_list_id' => $FoodList->id,
                    'meal_id' => $meal->id,
                    'amount' => $flm['mass_in_grams'],
                    'unit_id' => $gramId,
                    'mass_in_grams' => $flm['mass_in_grams'],
                    'seq_num' => $idx,
                ]);
                $foodListMassInGrams += $flm['mass_in_grams'];
            }

            $FoodList->update([
                'mass_in_grams' => $foodListMassInGrams
            ]);

            // Create a food-list-specific unit
            Unit::create([
                'name' => 'food list',
                'seq_num' => -1,
                'food_list_id' => $FoodList->id,
                'custom_unit_amount' => 1,
                'custom_mass_amount' => $foodListMassInGrams,
                'custom_mass_unit_id' => $gramId,
                'custom_grams' => $foodListMassInGrams,
            ]);


        }


    }
}
