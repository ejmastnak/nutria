<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FoodIntakeRecord;
use App\Models\Unit;
use App\Models\Ingredient;
use App\Models\Meal;
use App\Services\UnitConversionService;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class FoodIntakeRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedIngredientIntakeRecords();
        $this->seedMealIntakeRecords();
    }

    private function seedIngredientIntakeRecords() {
        $json = Storage::disk('seeders')->get('json/ingredient-intake-records.json');
        $ingredientIntakeRecords = json_decode($json, true);

        foreach ($ingredientIntakeRecords as $ingredientIntakeRecord) {

            $unit = Unit::where('name', $ingredientIntakeRecord['unit'])->first();
            if (is_null($unit)) {
                $this->command->info("Warning. Failed to find unit " . $ingredientIntakeRecord['unit'] . " when seeding FoodIntakeRecords.");
                continue;
            }

            $ingredient = Ingredient::where('name', $ingredientIntakeRecord['ingredient'])->first();
            if (is_null($ingredient)) {
                $this->command->info("Warning. Failed to find ingredient " . $ingredientIntakeRecord['ingredient'] . " when seeding FoodIntakeRecords.");
                continue;
            }

            FoodIntakeRecord::updateOrCreate([
                'ingredient_id' => $ingredient->id,
                'meal_id' => null,
                'amount' => $ingredientIntakeRecord['amount'],
                'unit_id' => $unit->id,
                'mass_in_grams' => UnitConversionService::convertToGrams($ingredientIntakeRecord['amount'], $unit->id, $ingredient->id, null, null),
                'date_time_utc' => Carbon::createFromFormat('Y-m-d', gmdate('Y-m-d', time()))->subDays($ingredientIntakeRecord['days_before_today'])->format('Y-m-d') . ' ' . $ingredientIntakeRecord['time'],
                'user_id' => $ingredientIntakeRecord['user_id'],
            ]);
        }
    }

    private function seedMealIntakeRecords() {
        $json = Storage::disk('seeders')->get('json/meal-intake-records.json');
        $mealIntakeRecords = json_decode($json, true);

        foreach ($mealIntakeRecords as $mealIntakeRecord) {

            $meal = Meal::where('name', $mealIntakeRecord['meal'])->first();
            if (is_null($meal)) {
                $this->command->info("Warning. Failed to find meal " . $mealIntakeRecord['meal'] . " when seeding FoodIntakeRecords.");
                continue;
            }

            if ($mealIntakeRecord['unit'] === 'meal') $unit = Unit::where('meal_id', $meal->id)->first();
            else $unit = Unit::where('name', $mealIntakeRecord['unit'])->first();

            if (is_null($unit)) {
                $this->command->info("Warning. Failed to find unit " . $mealIntakeRecord['unit'] . " when seeding FoodIntakeRecords.");
                continue;
            }

            FoodIntakeRecord::updateOrCreate([
                'ingredient_id' => null,
                'meal_id' => $meal->id,
                'amount' => $mealIntakeRecord['amount'],
                'unit_id' => $unit->id,
                'mass_in_grams' => UnitConversionService::convertToGrams($mealIntakeRecord['amount'], $unit->id, null, $meal->id, null),
                'date_time_utc' => Carbon::createFromFormat('Y-m-d', gmdate('Y-m-d', time()))->subDays($mealIntakeRecord['days_before_today'])->format('Y-m-d') . ' ' . $mealIntakeRecord['time'],
                'user_id' => $mealIntakeRecord['user_id'],
            ]);
        }
    }

}
