<?php

namespace Database\Seeders;

use App\Models\IngredientCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class IngredientCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = Storage::disk('seeders')->get('json/ingredient-categories.json');
        $ingredient_categories = json_decode($json, true);

        foreach ($ingredient_categories as $ingredient_category) {
            IngredientCategory::updateOrCreate([
                'id' => $ingredient_category['fdc_id'],
                'name' => $ingredient_category['name'],
            ]);
        }
    }
}
