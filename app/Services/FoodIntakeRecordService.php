<?php
namespace App\Services;

use App\Models\FoodIntakeRecord;
use App\Services\UnitConversionService;
use Illuminate\Support\Facades\DB;

class FoodIntakeRecordService
{
    public function storeManyIngredientIntakeRecords(array $data, int $userId): void
    {
        DB::transaction(function () use ($data, $userId) {
            foreach ($data['ingredient_intake_records'] as $ingredientIntakeRecord) {
                $this->storeIngredientIntakeRecord($ingredientIntakeRecord, $userId);
            }
        });
    }

    public function storeIngredientIntakeRecord(array $data, int $userId): ?int
    {
        $ingredientIntakeRecord = FoodIntakeRecord::create([
            'amount' => $data['amount'],
            'ingredient_id' => $data['ingredient_id'],
            'meal_id' => null,
            'unit_id' => $data['unit_id'],
            'mass_in_grams' => UnitConversionService::convertToGrams($data['amount'], $data['unit_id'], $data['ingredient_id'], null, null),
            'date_time_utc' => $data['date_time_utc'],
            'description' => $data['description'],
            'user_id' => $userId,
        ]);
        return $ingredientIntakeRecord->id;
    }

    public function updateIngredientIntakeRecord(array $data, FoodIntakeRecord $ingredientIntakeRecord): void
    {
        $ingredientIntakeRecord->update([
            'amount' => $data['amount'],
            'ingredient_id' => $data['ingredient_id'],
            'unit_id' => $data['unit_id'],
            'mass_in_grams' => UnitConversionService::convertToGrams($data['amount'], $data['unit_id'], $data['ingredient_id'], null, null),
            'date_time_utc' => $data['date_time_utc'],
            'description' => $data['description'],
        ]);
    }

    public function storeManyMealIntakeRecords(array $data, int $userId): void
    {
        DB::transaction(function () use ($data, $userId) {
            foreach ($data['meal_intake_records'] as $mealIntakeRecord) {
                $this->storeMealIntakeRecord($mealIntakeRecord, $userId);
            }
        });
    }

    public function storeMealIntakeRecord(array $data, int $userId): ?int
    {
        $mealIntakeRecord = FoodIntakeRecord::create([
            'amount' => $data['amount'],
            'ingredient_id' => null,
            'meal_id' => $data['meal_id'],
            'unit_id' => $data['unit_id'],
            'mass_in_grams' => UnitConversionService::convertToGrams($data['amount'], $data['unit_id'], null, $data['meal_id'], null),
            'date_time_utc' => $data['date_time_utc'],
            'description' => $data['description'],
            'user_id' => $userId,
        ]);
        return $mealIntakeRecord->id;
    }

    public function updateMealIntakeRecord(array $data, FoodIntakeRecord $mealIntakeRecord): void
    {
        $mealIntakeRecord->update([
            'amount' => $data['amount'],
            'meal_id' => $data['meal_id'],
            'unit_id' => $data['unit_id'],
            'mass_in_grams' => UnitConversionService::convertToGrams($data['amount'], $data['unit_id'], null, $data['meal_id'], null),
            'date_time_utc' => $data['date_time_utc'],
            'description' => $data['description'],
        ]);
    }

    public function deleteFoodIntakeRecord(FoodIntakeRecord $foodIntakeRecord): array
    {
        $success = $foodIntakeRecord->delete();
        if ($success) $message = 'Success! Record deleted successfully.';
        else $message = 'Error. Failed to delete record.';

        return [
            'success' => $success,
            'restricted' => false,
            'message' => $message,
            'errors' => [],
        ];
    }

}
