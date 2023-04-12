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
            $meal_data = json_decode($json, true);
            $meal_mass_in_grams = 0;
            $meal = Meal::create([
                'name' => $meal_data['name'],
                'mass_in_grams' => $meal_mass_in_grams
            ]);
            foreach($meal_data['meal_ingredients'] as $mi) {
                MealIngredient::create([
                    'meal_id' => $meal->id,
                    'ingredient_id' => $mi['ingredient_id'],
                    'amount' => $mi['mass_in_grams'],
                    'unit_id' => $gram_id,
                    'mass_in_grams' => $mi['mass_in_grams'],
                ]);
                $meal_mass_in_grams += $mi['mass_in_grams'];
            }
            $meal->update([
              'mass_in_grams' => $meal_mass_in_grams
            ]);
        }
    }
}
