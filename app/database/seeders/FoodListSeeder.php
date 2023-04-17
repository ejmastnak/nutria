<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Unit;
use App\Models\FoodList;
use App\Models\FoodListIngredient;
use App\Models\FoodListMeal;

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
            $food_list = json_decode($json, true);

            $food_list_id = FoodList::create([
                'name' => $food_list['name'],
                'user_id' => 1
            ])->id;

            foreach($food_list['food_list_ingredients'] as $fli) {
                FoodListIngredient::create([
                    'food_list_id' => $food_list_id,
                    'ingredient_id' => $fli['ingredient_id'],
                    'amount' => $fli['mass_in_grams'],
                    'unit_id' => $gram_id,
                    'mass_in_grams' => $fli['mass_in_grams'],
                ]);
            }

            foreach($food_list['food_list_meals'] as $flm) {
                FoodListMeal::create([
                    'food_list_id' => $food_list_id,
                    'meal_id' => $flm['meal_id'],
                    'amount' => $flm['mass_in_grams'],
                    'unit_id' => $gram_id,
                    'mass_in_grams' => $flm['mass_in_grams'],
                ]);
            }

        }
    }
}
