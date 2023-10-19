<?php

namespace Database\Seeders;

use App\Models\Nutrient;
use App\Models\NutrientCategory;
use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class NutrientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = Storage::disk('seeders')->get('json/nutrients.json');
        $nutrients = json_decode($json, true);

        foreach ($nutrients as $nutrient) {

            $nutrientCategory = NutrientCategory::where('name', $nutrient['nutrient_category_name'])->first();
            if (is_null($nutrientCategory)) {
                $this->command->info('Warning: Failed to find nutrient category ' . $nutrient['nutrient_category_name'] . ' when seeding Nutrients.');
                continue;
            }

            $unit = Unit::where('name', $nutrient['unit_name'])->first();
            if (is_null($unit)) {
                $this->command->info('Warning: Failed to find unit ' . $nutrient['unit_name'] . ' when seeding Nutrients.');
                continue;
            }

            Nutrient::updateOrCreate([
                'id' => $nutrient['fdc_id'],
                'name' => $nutrient['name'],
                'display_name' => $nutrient['display_name'],
                'unit_id' => $unit->id,
                'nutrient_category_id' => $nutrientCategory->id,
                'precision' => $nutrient['precision'],
                'display_order' => $nutrient['display_order'],
            ]);
        }
    }
}
