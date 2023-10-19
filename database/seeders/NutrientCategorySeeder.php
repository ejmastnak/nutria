<?php

namespace Database\Seeders;

use App\Models\NutrientCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class NutrientCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = Storage::disk('seeders')->get('json/nutrient-categories.json');
        $nutrientCategories = json_decode($json, true);

        foreach ($nutrientCategories as $nutrientCategory) {
            NutrientCategory::updateOrCreate([
                'name' => $nutrientCategory,
            ]);
        }
    }
}
