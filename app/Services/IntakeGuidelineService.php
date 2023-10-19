<?php
namespace App\Services;

use App\Models\IntakeGuideline;
use App\Models\IntakeGuidelineNutrient;
use Illuminate\Support\Facades\DB;

class IntakeGuidelineService
{
    public function storeIntakeGuideline(array $data, int $userId): ?IntakeGuideline
    {
        $intakeGuideline = null;
        DB::transaction(function () use ($data, $userId, &$intakeGuideline) {

            // Create intake guideline
            $intakeGuideline = IntakeGuideline::create([
                'name' => $data['name'],
                'user_id' => $userId,
            ]);

            // Create intake guideline's nutrients
            foreach ($data['intake_guideline_nutrients'] as $ign) {
                IntakeGuidelineNutrient::create([
                    'intake_guideline_id' => $intakeGuideline->id,
                    'nutrient_id' => $ign['nutrient_id'],
                    'rdi' => $ign['rdi'],
                ]);
            }

        });
        return $intakeGuideline;
    }

    public function updateIntakeGuideline(array $data, IntakeGuideline $intakeGuideline): ?IntakeGuideline
    {
        DB::transaction(function () use ($data, $intakeGuideline) {

            // Update intake guideline
            $intakeGuideline->update([ 'name' => $data['name'] ]);

            // Update ingredient's nutrients
            foreach ($data['intake_guideline_nutrients'] as $ign) {
                $IntakeGuidelineNutrient = IntakeGuidelineNutrient::find($ign['id']);
                $IntakeGuidelineNutrient->update([
                    'rdi' => $ign['rdi'],
                ]);
            }
        });
        return $intakeGuideline;
    }

}
