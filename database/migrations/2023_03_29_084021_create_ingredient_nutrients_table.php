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
        Schema::create('ingredient_nutrients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_id')->references('id')->on('ingredients')->cascadeOnDelete();
            $table->foreignId('nutrient_id')->references('id')->on('nutrients')->cascadeOnDelete();
            $table->decimal('amount', 10, 4)->default(0.0);
            $table->decimal('amount_per_100g', 10, 4)->default(0.0);
            $table->index(['ingredient_id', 'nutrient_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredient_nutrients');
    }
};
