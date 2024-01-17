<?php
namespace App\Services;

use App\Models\IngredientIntakeRecord;
use App\Services\UnitConversionService;
use Illuminate\Support\Facades\DB;

class IngredientIntakeRecordService
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
        $ingredientIntakeRecord = IngredientIntakeRecord::create([
            'amount' => $data['amount'],
            'ingredient_id' => $data['ingredient_id'],
            'unit_id' => $data['unit_id'],
            'mass_in_grams' => UnitConversionService::convertToGrams($data['amount'], $data['unit_id'], $data['ingredient_id'], null, null),
            'date_time_utc' => $data['date_time_utc'],
            'user_id' => $userId,
        ]);
        return $ingredientIntakeRecord->id;
    }

    public function updateIngredientIntakeRecord(array $data, IngredientIntakeRecord $ingredientIntakeRecord): void
    {
        $ingredientIntakeRecord->update([
            'amount' => $data['amount'],
            'ingredient_id' => $data['ingredient_id'],
            'unit_id' => $data['unit_id'],
            'mass_in_grams' => UnitConversionService::convertToGrams($data['amount'], $data['unit_id'], $data['ingredient_id'], null, null),
            'date_time_utc' => $data['date_time_utc'],
        ]);
    }

    public function deleteIngredientIntakeRecord(IngredientIntakeRecord $ingredientIntakeRecord): array
    {
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
