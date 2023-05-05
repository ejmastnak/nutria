<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
        DB::table('intake_guideline_nutrients')->truncate();
        DB::table('intake_guidelines')->truncate();

        foreach(glob(__DIR__ . '/intake_guidelines/*.json') as $file) {
            $json = file_get_contents($file);
            $intake_guideline = json_decode($json, true);

            $intake_guideline_id = IntakeGuideline::create([
                'name' => $intake_guideline['name'],
                'user_id' => $intake_guideline['user_id'],
            ])->id;

            foreach($intake_guideline['nutrients'] as $nutrient) {

                # Check a nutrient with specified nutrient_id exists in database
                $db_nutrient = Nutrient::find($nutrient['nutrient_id']);
                if (!$db_nutrient) {
                    $this->command->info("Warning: did not find nutrient with name " . $nutrient['name'] . " and id " . $nutrient['nutrient_id'] . " in database when seeding IntakeGuidelines.");
                    continue;
                }

                # Double check that nutrient unit in seed file is consistent
                # with the nutrient's preferred unit in database.
                $seed_unit_id = Unit::where('name', $nutrient['unit'])->first()->id;
                if($seed_unit_id !== $db_nutrient->unit_id) {
                    $this->command->info("Error seeding IntakeGuidelines: unrecognized unit for ingredient " . $nutrient['name'] . " when seeding intake guideline " . $intake_guideline['name']);
                    continue;
                }

                IntakeGuidelineNutrient::create([
                    'intake_guideline_id' => $intake_guideline_id,
                    'nutrient_id' => $nutrient['nutrient_id'],
                    'rdi' => $nutrient['rdi']
                ]);
            }
        }
    }
}
