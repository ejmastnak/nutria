<?php
namespace App\Services;

use App\Models\BodyWeightRecord;
use App\Services\UnitConversionService;
use Illuminate\Support\Facades\DB;

class BodyWeightRecordService
{
    public function storeManyBodyWeightRecords(array $data, int $userId): void
    {
        DB::transaction(function () use ($data, $userId) {
            foreach ($data['body_weight_records'] as $bodyWeightRecord) {
                $this->storeBodyWeightRecord($bodyWeightRecord, $userId);
            }
        });
    }

    public function storeBodyWeightRecord(array $data, int $userId): ?int
    {
        $bodyWeightRecord = BodyWeightRecord::create([
            'amount' => $data['amount'],
            'unit_id' => $data['unit_id'],
            'kg' => UnitConversionService::convertToKilograms($data['amount'], $data['unit_id']),
            'lb' => UnitConversionService::convertToPounds($data['amount'], $data['unit_id']),
            'date_time_utc' => $data['date_time_utc'],
            'user_id' => $userId,
        ]);
        return $bodyWeightRecord->id;
    }

    public function updateBodyWeightRecord(array $data, BodyWeightRecord $bodyWeightRecord): void
    {
        $bodyWeightRecord->update([
            'amount' => $data['amount'],
            'unit_id' => $data['unit_id'],
            'kg' => UnitConversionService::convertToKilograms($data['amount'], $data['unit_id']),
            'lb' => UnitConversionService::convertToPounds($data['amount'], $data['unit_id']),
            'date_time_utc' => $data['date_time_utc'],
        ]);
    }

    public function deleteBodyWeightRecord(BodyWeightRecord $bodyWeightRecord): void
    {
        $success = $bodyWeightRecord->delete();
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
