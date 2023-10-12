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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->double('g', 10, 4)->nullable();
            $table->double('ml', 10, 4)->nullable();
            $table->integer('seq_num');
            // ingredient_id is added after ingredients table is created
            // meal_id is added after meals table is created
            $table->double('custom_unit_amount', 10, 4)->nullable();
            $table->double('custom_mass_amount', 10, 4)->nullable();
            $table->foreignId('custom_mass_unit_id')->nullable()->references('id')->on('units');
            $table->double('custom_grams', 10, 4)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
