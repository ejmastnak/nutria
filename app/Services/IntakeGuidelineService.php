<?php
namespace App\Services;

use App\Models\IntakeGuideline;
use App\Models\IntakeGuidelineNutrient;
use Illuminate\Support\Facades\DB;

class IntakeGuidelineService
{
    public function storeIntakeGuideline(array $data, int $userId): ?int
    {
        $intakeGuideline = null;
        DB::transaction(function () use ($data, $userId, &$intakeGuideline) {

            // Create intake guideline
            $intakeGuideline = IntakeGuideline::create([
                'name' => $data['name'],
                'priority' => array_key_exists('priority', $data) ? $data['priority'] : null,
                'description' => $data['description'],
                'user_id' => $userId,
            ]);

            // Create intake guideline's nutrients
            foreach ($data['intake_guideline_nutrients'] as $ign) {
                IntakeGuidelineNutrient::create([
                    'intake_guideline_id' => $intakeGuideline->id,
                    'nutrient_id' => $ign['nutrient_id'],
                    'rdi' => $ign['rdi'] ? $ign['rdi'] : 0.0,
                ]);
            }

        });
        return $intakeGuideline->id;
    }

    public function updateIntakeGuideline(array $data, IntakeGuideline $intakeGuideline): void
    {
        DB::transaction(function () use ($data, $intakeGuideline) {

            // Update intake guideline
            $intakeGuideline->update([
                'name' => $data['name'],
                'priority' => array_key_exists('priority', $data) ? $data['priority'] : null,
                'description' => $data['description'],
            ]);

            // Update ingredient's nutrients
            foreach ($data['intake_guideline_nutrients'] as $ign) {
                $IntakeGuidelineNutrient = IntakeGuidelineNutrient::find($ign['id']);
                $IntakeGuidelineNutrient->update([
                    'rdi' => $ign['rdi'] ? $ign['rdi'] : 0.0,
                ]);
            }
        });
    }

    public function deleteIntakeGuideline(IntakeGuideline $intakeGuideline): array
    {
        $success = $intakeGuideline->delete();
        if ($success) $message = 'Success! Intake Guideline deleted successfully.';
        else $message = 'Failed to delete intake guideline.';
        return [
            'success' => $success,
            'message' => $message,
        ];
    }

}
