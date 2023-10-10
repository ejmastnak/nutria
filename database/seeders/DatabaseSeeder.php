<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            UnitSeeder::class,
            NutrientCategorySeeder::class,
            NutrientSeeder::class,
            IngredientCategorySeeder::class,
            IngredientSeeder::class,
            MealSeeder::class,
            FoodListSeeder::class,
            IntakeGuidelineSeeder::class,
        ]);
    }
}
