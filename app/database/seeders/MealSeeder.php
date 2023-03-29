<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

        foreach(glob(__DIR__ . '/meals/*.json') as $file) {
            $json = file_get_contents($file);
            $meal = json_decode($json, true);
            Meal::create(['name' => $meal['name']]);
        }
    }
}
