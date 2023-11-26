<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\Models\Unit;
use App\Models\Nutrient;
use App\Models\IntakeGuideline;
use App\Models\IntakeGuidelineNutrient;

class IntakeGuidelineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seed('json/intake-guidelines.json');
        if (\App::environment('local')) $this->seed('json/intake-guidelines-local.json');
    }

    private function seed(string $file) {
        $json = Storage::disk('seeders')->get($file);
        $intakeGuidelines = json_decode($json, true);

        foreach ($intakeGuidelines as $intakeGuideline) {

            $IntakeGuideline = IntakeGuideline::updateOrCreate([
                'name' => $intakeGuideline['name'],
                'user_id' => $intakeGuideline['user_id'],
            ]);

            foreach($intakeGuideline['intake_guideline_nutrients'] as $ign) {

                # Check a nutrient with specified nutrient_id exists in database
                $nutrient = Nutrient::find($ign['nutrient_id']);
                if (is_null($nutrient)) {
                    $this->command->info("Warning: did not find nutrient with name " . $ign['name'] . " and id " . $ign['nutrient_id'] . " in database when seeding IntakeGuidelines.");
                    continue;
                }

                # Double check that nutrient unit in seed file is consistent
                # with the nutrient's preferred unit in database.
                $unit = Unit::where('name', $ign['unit'])->first();
                if (is_null($unit)) {
                    $this->command->info("Warning: unrecognized unit " . $ign['unit'] . " when seeding IntakeGuidelines.");
                    continue;
                }
                if($unit->id !== $nutrient->unit_id) {
                    $this->command->info("Warning: unit " . $ign['unit'] . " is not compatible with nutrient " . $nutrient->name . " when seeding IntakeGuidelines.");
                    continue;
                }

                IntakeGuidelineNutrient::updateOrCreate([
                    'intake_guideline_id' => $IntakeGuideline->id,
                    'nutrient_id' => $ign['nutrient_id'],
                    'rdi' => $ign['rdi'],
                ]);
            }

        }
    }
}
