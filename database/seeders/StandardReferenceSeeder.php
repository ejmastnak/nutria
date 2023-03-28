<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Process;

class StandardReferenceSeeder extends Seeder
{
    /**
     * Seed the database with FoodData Central's Standard Reference tables
     */
    public function run(): void
    {
        $result = Process::path(__DIR__ . '/psql')->run('make seed');
        if($result->successful()) {
            $this->command->info('Successfully seeded units, nutrients, ingredient_categories, ingredients, and ingredient_nutrients tables');
        } else {
            $this->command->info('Failed seeding SR tables.');
            $this->command->info('Exit code: ' . $result->exitCode());
            $this->command->info('Error message: ' . $result->errorOutput());
        }
    }
}
