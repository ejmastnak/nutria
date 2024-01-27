<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('ingredient_intake_records');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('ingredient_intake_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_id')->references('id')->on('ingredients');
            $table->decimal('amount', 10, 4);
            $table->foreignId('unit_id')->references('id')->on('units');
            $table->decimal('mass_in_grams', 10, 4);
            $table->dateTime('date_time_utc');
            $table->foreignId('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }
};
