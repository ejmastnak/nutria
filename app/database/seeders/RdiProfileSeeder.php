<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Unit;
use App\Models\Nutrient;
use App\Models\RdiProfile;
use App\Models\RdiProfileNutrient;

class RdiProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('rdi_profile_nutrients')->truncate();
        DB::table('rdi_profiles')->truncate();

        foreach(glob(__DIR__ . '/rdi_profiles/*.json') as $file) {
            $json = file_get_contents($file);
            $rdi_profile = json_decode($json, true);

            $rdi_profile_id = RdiProfile::create([
                'name' => $rdi_profile['name'],
                'user_id' => $rdi_profile['user_id'],
            ])->id;

            foreach($rdi_profile['nutrients'] as $nutrient) {

                # Check a nutrient with specified nutrient_id exists in database
                $db_nutrient = Nutrient::find($nutrient['nutrient_id']);
                if (!$db_nutrient) {
                    $this->command->info("Warning: did not find nutrient with name " . $nutrient['name'] . " and id " . $nutrient['nutrient_id'] . " in database when seeding RdiProfiles.");
                    continue;
                }

                # Double check that nutrient unit in seed file is consistent
                # with the nutrient's preferred unit in database.
                $seed_unit_id = Unit::where('name', $nutrient['unit'])->first()->id;
                if($seed_unit_id !== $db_nutrient->unit_id) {
                    $this->command->info("Error seeding RdiProfiles: unrecognized unit for ingredient " . $nutrient['name'] . " when seeding RDI profile " . $rdi_profile['name']);
                    continue;
                }

                RdiProfileNutrient::create([
                    'rdi_profile_id' => $rdi_profile_id,
                    'nutrient_id' => $nutrient['nutrient_id'],
                    'rdi' => $nutrient['rdi']
                ]);
            }
        }
    }
}
