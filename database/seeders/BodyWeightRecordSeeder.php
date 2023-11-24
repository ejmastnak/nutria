<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BodyWeightRecord;
use App\Models\Unit;
use App\Services\UnitConversionService;
use Illuminate\Support\Facades\Storage;

class BodyWeightRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = Storage::disk('seeders')->get('json/body-weight-records.json');
        $bodyWeightRecords = json_decode($json, true);

        foreach ($bodyWeightRecords as $bodyWeightRecord) {
            $unit = Unit::where('name', $bodyWeightRecord['unit'])->first();
            if (is_null($unit)) {
                $this->command->info("Warning. Failed to find unit " . $bodyWeightRecord['unit'] . " when seeding BodyWeightRecords.");
                continue;
            }
            BodyWeightRecord::updateOrCreate([
                'amount' => $bodyWeightRecord['amount'],
                'unit_id' => $unit->id,
                'kg' => UnitConversionService::convertToKilograms($bodyWeightRecord['amount'], $unit->id),
                'lb' => UnitConversionService::convertToPounds($bodyWeightRecord['amount'], $unit->id),
                'date' => $bodyWeightRecord['date'],
                'time' => isset($bodyWeightRecord['time']) ? $bodyWeightRecord['time'] : null,
                'user_id' => $bodyWeightRecord['user_id'],
            ]);
        }
    }
}
