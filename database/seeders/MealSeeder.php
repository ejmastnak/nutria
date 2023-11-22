<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\Meal;
use App\Models\MealIngredient;
use App\Models\Ingredient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class MealSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gramId = Unit::gramId();

        $json = Storage::disk('seeders')->get('json/meals.json');
        $meals = json_decode($json, true);

        foreach ($meals as $meal) {

            $mealMassInGrams = 0;
            $Meal = Meal::updateOrCreate(
                [
                    'name' => $meal['name'],
                    'user_id' => $meal['user_id'],
                ],
                [ 'mass_in_grams' => $mealMassInGrams ]
            );
            foreach($meal['meal_ingredients'] as $idx=>$mealIngredient) {

                $ingredient = Ingredient::where('name', $mealIngredient['name'])->first();
                if (is_null($ingredient)) {
                    $this->command->info("Warning. Failed to find ingredient " . $mealIngredient['name'] . " when seeding Meals.");
                    continue;
                }

                MealIngredient::updateOrCreate([
                    'meal_id' => $Meal->id,
                    'ingredient_id' => $ingredient->id,
                    'amount' => $mealIngredient['mass_in_grams'],
                    'unit_id' => $gramId,
                    'mass_in_grams' => $mealIngredient['mass_in_grams'],
                    'seq_num' => $idx,
                ]);
                $mealMassInGrams += $mealIngredient['mass_in_grams'];

            }
            $Meal->update([
                'mass_in_grams' => $mealMassInGrams,
            ]);

            // Create a meal-specific unit
            Unit::create([
                'name' => 'meal',
                'seq_num' => -1,
                'meal_id' => $Meal->id,
                'custom_unit_amount' => 1,
                'custom_mass_amount' => $mealMassInGrams,
                'custom_mass_unit_id' => $gramId,
                'custom_grams' => $mealMassInGrams,
            ]);

        }
    }
}
