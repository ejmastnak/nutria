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
            $food_list_id = FoodList::create(['name' => $food_list['name']])->id;

            foreach($food_list['food_list_items'] as $item) {
                if($item['ingredient_id']) {
                    FoodListIngredient::create([
                        'food_list_id' => $food_list_id,
                        'ingredient_id' => $item['ingredient_id'],
                        'amount' => $item['mass_in_grams'],
                        'unit_id' => $gram_id,
                        'mass_in_grams' => $item['mass_in_grams'],
                    ]);
                } else if($item['meal_id']) {
                    FoodListMeal::create([
                        'food_list_id' => $food_list_id,
                        'meal_id' => $item['meal_id'],
                        'amount' => $item['mass_in_grams'],
                        'unit_id' => $gram_id,
                        'mass_in_grams' => $item['mass_in_grams'],
                    ]);
                }
            }
        }
    }
}
