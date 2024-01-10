<?php
namespace App\Services;

use App\Models\BodyWeightRecord;
use App\Services\UnitConversionService;
use Illuminate\Support\Facades\DB;

class BodyWeightRecordService
{
    public function storeBodyWeightRecords(array $data, int $userId): ?bool
    {
        DB::transaction(function () use ($data, $userId) {
            foreach ($data['body_weight_records'] as $bodyWeightRecord) {
                $this->storeBodyWeightRecord($bodyWeightRecord, $userId);
            }
        });
        return true;
    }

    public function storeBodyWeightRecord(array $data, int $userId): ?BodyWeightRecord
    {
        return BodyWeightRecord::create([
            'amount' => $data['amount'],
            'unit_id' => $data['unit_id'],
            'kg' => UnitConversionService::convertToKilograms($data['amount'], $data['unit_id']),
            'lb' => UnitConversionService::convertToPounds($data['amount'], $data['unit_id']),
            'date_time_utc' => $data['date_time_utc'],
            'user_id' => $userId,
        ]);
    }

    public function updateBodyWeightRecord(array $data, BodyWeightRecord $bodyWeightRecord): ?BodyWeightRecord
    {
        $bodyWeightRecord->update([
            'amount' => $data['amount'],
            'unit_id' => $data['unit_id'],
            'kg' => UnitConversionService::convertToKilograms($data['amount'], $data['unit_id']),
            'lb' => UnitConversionService::convertToPounds($data['amount'], $data['unit_id']),
            'date_time_utc' => $data['date_time_utc'],
        ]);
        return $bodyWeightRecord;
    }

    public function deleteBodyWeightRecord(BodyWeightRecord $bodyWeightRecord) {
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
