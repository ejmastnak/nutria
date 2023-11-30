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
        $result = Process::path(storage_path('app/seeders/psql'))
        ->env([
            'DB' => config('database.connections.' . config('database.default') . '.database'),
            'DB_USERNAME' => config('database.connections.' . config('database.default') . '.username'),
            'SEED_INGREDIENTS_FROM_WHITELIST' => 1,
        ])
        ->run("make seed");
        $this->command->info("stdout\n" . $result->output());
        $this->command->info("stderr\n" . $result->errorOutput());
        if($result->failed()) {
            $this->command->info('Failed seeding Ingredients and IngredientNutrients.');
            $this->command->info('Exit code: ' . $result->exitCode());
            $this->command->info('Error message: ' . $result->errorOutput());
        }
    }
}
