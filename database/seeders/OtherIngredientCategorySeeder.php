<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\IngredientCategory;

/**
 *  Simple class to manually add an "Other" ingredient category.
 */
class OtherIngredientCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (is_null(IngredientCategory::where('name', 'Other')->first())) {
            IngredientCategory::create([
                'id' => IngredientCategory::max('id') + 1,
                'name' => 'Other'
            ]);
        }
    }
}
