<?php
namespace App\Services;

use App\Models\BodyWeightRecord;
use App\Services\UnitConversionService;

class BodyWeightRecordService
{
    public function storeBodyWeightRecord(array $data, int $userId): ?BodyWeightRecord
    {
        return BodyWeightRecord::create([
            'amount' => $data['amount'],
            'unit_id' => $data['unit_id'],
            'kg' => UnitConversionService::convertToKilograms($data['amount'], $data['unit_id']),
            'lb' => UnitConversionService::convertToPounds($data['amount'], $data['unit_id']),
            'date' => $data['date'],
            'time' => $data['time'],
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
            'date' => $data['date'],
            'time' => $data['time'],
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
