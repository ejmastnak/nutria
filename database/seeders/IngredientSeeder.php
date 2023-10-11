<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Process;

class IngredientSeeder extends Seeder
{
    /**
     * Seed the database with Ingredients and IngredientNutrient.
     * Uses data from FoodData Central's Standard Reference `food` and
     * `food_nutrient` tables.
     */
    public function run(): void
    {
        $result = Process::path(storage_path('app/seeders/psql'))->run('make seed');
        if($result->successful()) {
            $this->command->info('Successfully seeded Ingredients and IngredientNutrients.');
        } else {
            $this->command->info('Failed seeding Ingredients and IngredientNutrients.');
            $this->command->info('Exit code: ' . $result->exitCode());
            $this->command->info('Error message: ' . $result->errorOutput());
        }
    }
}