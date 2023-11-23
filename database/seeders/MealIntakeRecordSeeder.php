<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MealIntakeRecord;
use App\Models\Unit;
use App\Models\Meal;
use App\Services\UnitConversionService;
use Illuminate\Support\Facades\Storage;

class MealIntakeRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = Storage::disk('seeders')->get('json/meal-intake-records.json');
        $mealIntakeRecords = json_decode($json, true);

        foreach ($mealIntakeRecords as $mealIntakeRecord) {

            $meal = Meal::where('name', $mealIntakeRecord['meal'])->first();
            if (is_null($meal)) {
                $this->command->info("Warning. Failed to find meal " . $mealIntakeRecord['meal'] . " when seeding MealIntakeRecords.");
                continue;
            }

            if ($mealIntakeRecord['unit'] === 'meal') $unit = Unit::where('meal_id', $meal->id)->first();
            else $unit = Unit::where('name', $mealIntakeRecord['unit'])->first();

            if (is_null($unit)) {
                $this->command->info("Warning. Failed to find unit " . $mealIntakeRecord['unit'] . " when seeding MealIntakeRecords.");
                continue;
            }

            MealIntakeRecord::updateOrCreate([
                'meal_id' => $meal->id,
                'amount' => $mealIntakeRecord['amount'],
                'unit_id' => $unit->id,
                'mass_in_grams' => UnitConversionService::convertToGrams($mealIntakeRecord['amount'], $unit->id, null, $meal->id, null),
                'date' => $mealIntakeRecord['date'],
                'time' => $mealIntakeRecord['time'],
                'user_id' => $mealIntakeRecord['user_id'],
            ]);
        }
    }
}
