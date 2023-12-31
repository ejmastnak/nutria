<?php
namespace App\Services;

use App\Models\FoodListIntakeRecord;
use App\Services\UnitConversionService;
use Illuminate\Support\Facades\DB;

class FoodListIntakeRecordService
{
    public function storeFoodListIntakeRecords(array $data, int $userId): ?bool
    {
        DB::transaction(function () use ($data, $userId) {
            foreach ($data['food_list_intake_records'] as $foodListIntakeRecord) {
                $this->storeFoodListIntakeRecord($foodListIntakeRecord, $userId);
            }
        });
        return true;
    }

    private function storeFoodListIntakeRecord(array $data, int $userId): ?FoodListIntakeRecord
    {
        return FoodListIntakeRecord::create([
            'amount' => $data['amount'],
            'food_list_id' => $data['food_list_id'],
            'unit_id' => $data['unit_id'],
            'mass_in_grams' => UnitConversionService::convertToGrams($data['amount'], $data['unit_id'], null, null, $data['food_list_id']),
            'date_time_utc' => $data['date_time_utc'],
            'user_id' => $userId,
        ]);
    }

    public function updateFoodListIntakeRecord(array $data, FoodListIntakeRecord $foodListIntakeRecord): ?FoodListIntakeRecord
    {
        $foodListIntakeRecord->update([
            'amount' => $data['amount'],
            'food_list_id' => $data['food_list_id'],
            'unit_id' => $data['unit_id'],
            'mass_in_grams' => UnitConversionService::convertToGrams($data['amount'], $data['unit_id'], null, null, $data['food_list_id']),
            'date_time_utc' => $data['date_time_utc'],
        ]);
        return $foodListIntakeRecord;
    }

    public function deleteFoodListIntakeRecord(FoodListIntakeRecord $foodListIntakeRecord) {
        $success = $foodListIntakeRecord->delete();
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
