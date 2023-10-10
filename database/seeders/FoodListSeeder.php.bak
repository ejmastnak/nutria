<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Unit;
use App\Models\FoodList;
use App\Models\FoodListIngredient;
use App\Models\FoodListMeal;
use App\Models\Ingredient;

class FoodListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('food_list_ingredients')->truncate();
        DB::table('food_list_meals')->truncate();
        DB::table('food_lists')->truncate();
        $gram_id = Unit::where('name', 'g')->first()->id;

        foreach(glob(__DIR__ . '/food_lists/*.json') as $file) {
            $json = file_get_contents($file);
            $food_list_data = json_decode($json, true);
            $food_list_mass_in_grams = 0;
            $food_list = FoodList::create([
                'name' => $food_list_data['name'],
                'mass_in_grams' => $food_list_mass_in_grams,
                'user_id' => 1
            ]);

            foreach($food_list_data['food_list_ingredients'] as $fli) {

                $ingredient_id = Ingredient::where('name', $fli['name'])->first()->id;
                FoodListIngredient::create([
                    'food_list_id' => $food_list->id,
                    'ingredient_id' => $ingredient_id,
                    'amount' => $fli['mass_in_grams'],
                    'unit_id' => $gram_id,
                    'mass_in_grams' => $fli['mass_in_grams'],
                ]);
                $food_list_mass_in_grams += $fli['mass_in_grams'];
            }

            foreach($food_list_data['food_list_meals'] as $flm) {
                FoodListMeal::create([
                    'food_list_id' => $food_list->id,
                    'meal_id' => $flm['meal_id'],
                    'amount' => $flm['mass_in_grams'],
                    'unit_id' => $gram_id,
                    'mass_in_grams' => $flm['mass_in_grams'],
                ]);
                $food_list_mass_in_grams += $flm['mass_in_grams'];
            }

            $food_list->update([
              'mass_in_grams' => $food_list_mass_in_grams
            ]);

        }
    }
}
