<?php
namespace App\Services;

use App\Models\MealIntakeRecord;
use App\Services\UnitConversionService;
use Illuminate\Support\Facades\DB;

class MealIntakeRecordService
{
    public function storeMealIntakeRecords(array $data, int $userId): ?bool
    {
        DB::transaction(function () use ($data, $userId) {
            foreach ($data['meal_intake_records'] as $mealIntakeRecord) {
                $this->storeMealIntakeRecord($mealIntakeRecord, $userId);
            }
        });
        return true;
    }

    private function storeMealIntakeRecord(array $data, int $userId): ?MealIntakeRecord
    {
        return MealIntakeRecord::create([
            'amount' => $data['amount'],
            'meal_id' => $data['meal_id'],
            'unit_id' => $data['unit_id'],
            'mass_in_grams' => UnitConversionService::convertToGrams($data['amount'], $data['unit_id'], null, $data['meal_id'], null),
            'date_time_utc' => $data['date_time_utc'],
            'user_id' => $userId,
        ]);
    }

    public function updateMealIntakeRecord(array $data, MealIntakeRecord $mealIntakeRecord): ?MealIntakeRecord
    {
        $mealIntakeRecord->update([
            'amount' => $data['amount'],
            'meal_id' => $data['meal_id'],
            'unit_id' => $data['unit_id'],
            'mass_in_grams' => UnitConversionService::convertToGrams($data['amount'], $data['unit_id'], null, $data['meal_id'], null),
            'date_time_utc' => $data['date_time_utc'],
        ]);
        return $mealIntakeRecord;
    }

    public function deleteMealIntakeRecord(MealIntakeRecord $mealIntakeRecord) {
        $success = $mealIntakeRecord->delete();
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
