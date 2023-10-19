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
        $ingredientCategories = json_decode($json, true);

        foreach ($ingredientCategories as $ingredientCategory) {
            IngredientCategory::updateOrCreate([
                'id' => $ingredientCategory['fdc_id'],
                'name' => $ingredientCategory['name'],
            ]);
        }
    }
}
