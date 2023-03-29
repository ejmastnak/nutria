<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Unit;
use App\Models\Meal;
use App\Models\MealIngredient;

class MealSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('meal_ingredients')->truncate();
        DB::table('meals')->truncate();
        $gram_id = Unit::where('name', 'g')->first()->id;

        foreach(glob(__DIR__ . '/meals/*.json') as $file) {
            $json = file_get_contents($file);
            $meal = json_decode($json, true);
            $meal_id = Meal::create(['name' => $meal['name']])->id;
            foreach($meal['ingredients'] as $ingredient) {
                MealIngredient::create([
                    'meal_id' => $meal_id,
                    'ingredient_id' => $ingredient['ingredient_id'],
                    'amount' => $ingredient['mass_in_grams'],
                    'unit_id' => $gram_id,
                    'mass_in_grams' => $ingredient['mass_in_grams'],
                ]);
            }
        }
    }
}
