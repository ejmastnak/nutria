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
        Schema::create('food_list_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('food_list_id')->references('id')->on('food_lists')->cascadeOnDelete();
            $table->foreignId('ingredient_id')->references('id')->on('ingredients');
            $table->decimal('amount', 10, 4);
            $table->foreignId('unit_id')->references('id')->on('units');
            $table->decimal('mass_in_grams', 10, 4);
            $table->integer('seq_num');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_list_ingredients');
    }
};
