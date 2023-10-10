<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = Storage::disk('seeders')->get('json/units.json');
        $units = json_decode($json, true);

        foreach ($units as $unit) {
            Unit::updateOrCreate([
                'name' => $unit['name'],
                'full_name' => $unit['full_name'],
                'to_grams' => $unit['to_grams'],
            ]);
        }
    }
}
