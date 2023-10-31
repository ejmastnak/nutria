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
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fdc_id')->nullable();
            $table->string('name');
            $table->foreignId('ingredient_category_id')->nullable()->references('id')->on('ingredient_categories');
            $table->decimal('ingredient_nutrient_amount', 10, 4);
            $table->foreignId('ingredient_nutrient_amount_unit_id')->references('id')->on('units');
            $table->foreignId('density_mass_unit_id')->nullable()->references('id')->on('units');
            $table->index('density_mass_unit_id');
            $table->decimal('density_mass_amount', 10, 4)->nullable();
            $table->foreignId('density_volume_unit_id')->nullable()->references('id')->on('units');
            $table->index('density_volume_unit_id');
            $table->decimal('density_volume_amount', 10, 4)->nullable();
            $table->decimal('density_g_ml', 10, 4)->nullable();
            $table->foreignId('user_id')->nullable()->references('id')->on('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
