<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\IngredientIntakeRecord;
use App\Models\Unit;
use App\Models\Ingredient;
use App\Services\UnitConversionService;
use Illuminate\Support\Facades\Storage;

class IngredientIntakeRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = Storage::disk('seeders')->get('json/ingredient-intake-records.json');
        $ingredientIntakeRecords = json_decode($json, true);

        foreach ($ingredientIntakeRecords as $ingredientIntakeRecord) {

            $unit = Unit::where('name', $ingredientIntakeRecord['unit'])->first();
            if (is_null($unit)) {
                $this->command->info("Warning. Failed to find unit " . $ingredientIntakeRecord['unit'] . " when seeding IngredientIntakeRecords.");
                continue;
            }

            $ingredient = Ingredient::where('name', $ingredientIntakeRecord['ingredient'])->first();
            if (is_null($ingredient)) {
                $this->command->info("Warning. Failed to find ingredient " . $ingredientIntakeRecord['ingredient'] . " when seeding IngredientIntakeRecords.");
                continue;
            }

            IngredientIntakeRecord::updateOrCreate([
                'ingredient_id' => $ingredient->id,
                'amount' => $ingredientIntakeRecord['amount'],
                'unit_id' => $unit->id,
                'mass_in_grams' => UnitConversionService::convertToGrams($ingredientIntakeRecord['amount'], $unit->id, $ingredient->id, null, null),
                'date_time_utc' => $ingredientIntakeRecord['date'] . ' ' . $ingredientIntakeRecord['time'],
                'user_id' => $ingredientIntakeRecord['user_id'],
            ]);
        }
    }
}
