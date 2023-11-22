<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FoodListIntakeRecord;
use App\Models\Unit;
use App\Models\FoodList;
use App\Services\UnitConversionService;
use Illuminate\Support\Facades\Storage;

class FoodListIntakeRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = Storage::disk('seeders')->get('json/food-list-intake-records.json');
        $foodListIntakeRecords = json_decode($json, true);

        foreach ($foodListIntakeRecords as $foodListIntakeRecord) {

            $foodList = FoodList::where('name', $foodListIntakeRecord['food_list'])->first();
            if (is_null($foodList)) {
                $this->command->info("Warning. Failed to find food list " . $foodListIntakeRecord['food_list'] . " when seeding FoodListIntakeRecords.");
                continue;
            }

            if ($foodListIntakeRecord['unit'] === 'foodList') $unit = Unit::where('food_list_id', $foodList->id)->first();
            else $unit = Unit::where('name', $foodListIntakeRecord['unit'])->first();
            if (is_null($unit)) {
                $this->command->info("Warning. Failed to find unit " . $foodListIntakeRecord['unit'] . " when seeding FoodListIntakeRecords.");
                continue;
            }

            FoodListIntakeRecord::updateOrCreate([
                'food_list_id' => $foodList->id,
                'amount' => $foodListIntakeRecord['amount'],
                'unit_id' => $unit->id,
                'mass_in_grams' => UnitConversionService::convertToGrams($foodListIntakeRecord['amount'], $foodListIntakeRecord['unit_id'], null, null, $foodList->id),
                'date' => $foodListIntakeRecord['date'],
                'time' => $foodListIntakeRecord['time'],
                'user_id' => $foodListIntakeRecord['user_id'],
            ]);
        }
    }
}
