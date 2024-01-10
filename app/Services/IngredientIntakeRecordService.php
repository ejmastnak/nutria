<?php
namespace App\Services;

use App\Models\IngredientIntakeRecord;
use App\Services\UnitConversionService;
use Illuminate\Support\Facades\DB;

class IngredientIntakeRecordService
{
    public function storeManyIngredientIntakeRecords(array $data, int $userId): ?bool
    {
        DB::transaction(function () use ($data, $userId) {
            foreach ($data['ingredient_intake_records'] as $ingredientIntakeRecord) {
                $this->storeIngredientIntakeRecord($ingredientIntakeRecord, $userId);
            }
        });
        return true;
    }

    public function storeIngredientIntakeRecord(array $ingredientIntakeRecord, int $userId): ?IngredientIntakeRecord
    {
        return IngredientIntakeRecord::create([
            'amount' => $ingredientIntakeRecord['amount'],
            'ingredient_id' => $ingredientIntakeRecord['ingredient_id'],
            'unit_id' => $ingredientIntakeRecord['unit_id'],
            'mass_in_grams' => UnitConversionService::convertToGrams($ingredientIntakeRecord['amount'], $ingredientIntakeRecord['unit_id'], $ingredientIntakeRecord['ingredient_id'], null, null),
            'date_time_utc' => $ingredientIntakeRecord['date_time_utc'],
            'user_id' => $userId,
        ]);
    }

    public function updateIngredientIntakeRecord(array $data, IngredientIntakeRecord $ingredientIntakeRecord): ?IngredientIntakeRecord
    {
        $ingredientIntakeRecord->update([
            'amount' => $data['amount'],
            'ingredient_id' => $data['ingredient_id'],
            'unit_id' => $data['unit_id'],
            'mass_in_grams' => UnitConversionService::convertToGrams($data['amount'], $data['unit_id'], $data['ingredient_id'], null, null),
            'date_time_utc' => $data['date_time_utc'],
        ]);
        return $ingredientIntakeRecord;
    }

    public function deleteIngredientIntakeRecord(IngredientIntakeRecord $ingredientIntakeRecord) {
        $success = $ingredientIntakeRecord->delete();
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
