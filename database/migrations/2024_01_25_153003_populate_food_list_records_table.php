<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\FoodIntakeRecord;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::transaction(function () {
            if (Schema::hasTable('ingredient_intake_records')) {
                $iirs = DB::table('ingredient_intake_records')->get();
                foreach ($iirs as $iir) {
                    FoodIntakeRecord::create([
                        'ingredient_id' => $iir->ingredient_id,
                        'meal_id' => null,
                        'amount' => $iir->amount,
                        'unit_id' => $iir->unit_id,
                        'mass_in_grams' => $iir->mass_in_grams,
                        'date_time_utc' => $iir->date_time_utc,
                        'description' => $iir->description,
                        'user_id' => $iir->user_id,
                        'created_at' => $iir->created_at,
                        'updated_at' => $iir->updated_at,
                    ]);
                }
            }
            if (Schema::hasTable('meal_intake_records')) {
                $mirs = DB::table('meal_intake_records')->get();
                foreach ($mirs as $mir) {
                    FoodIntakeRecord::create([
                        'ingredient_id' => null,
                        'meal_id' => $mir->meal_id,
                        'amount' => $mir->amount,
                        'unit_id' => $mir->unit_id,
                        'mass_in_grams' => $mir->mass_in_grams,
                        'date_time_utc' => $mir->date_time_utc,
                        'description' => $mir->description,
                        'user_id' => $mir->user_id,
                        'created_at' => $mir->created_at,
                        'updated_at' => $mir->updated_at,
                    ]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('food_intake_records')) {
            DB::table('food_intake_records')->truncate();
        }
    }
};
