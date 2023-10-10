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
        $gram_id = Unit::where('name', 'g')->first()->id;

        $json = Storage::disk('seeders')->get('json/meals.json');
        $meals = json_decode($json, true);

        foreach ($meals as $meal) {

            $meal_mass_in_grams = 0;
            $Meal = Meal::updateOrCreate(
                [
                    'name' => $meal['name'],
                    'user_id' => $meal['user_id'],
                ],
                [ 'mass_in_grams' => $meal_mass_in_grams ]
            );
            foreach($meal['meal_ingredients'] as $idx=>$meal_ingredient) {

                $ingredient = Ingredient::where('name', $meal_ingredient['name'])->first();
                if (is_null($ingredient)) {
                    $this->command->info("Warning. Failed to find ingredient " . $meal_ingredient['name'] . " when seeding Meals.");
                    continue;
                }

                MealIngredient::updateOrCreate([
                    'meal_id' => $Meal->id,
                    'ingredient_id' => $ingredient->id,
                    'amount' => $meal_ingredient['mass_in_grams'],
                    'unit_id' => $gram_id,
                    'mass_in_grams' => $meal_ingredient['mass_in_grams'],
                    'idx' => $idx,
                ]);
                $meal_mass_in_grams += $meal_ingredient['mass_in_grams'];

            }
            $Meal->update([
                'mass_in_grams' => $meal_mass_in_grams,
            ]);
        }


    }
}
