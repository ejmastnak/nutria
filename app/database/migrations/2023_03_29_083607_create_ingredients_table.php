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
            $table->unsignedBigInteger('ingredient_category_id')->nullable();
            $table->foreign('ingredient_category_id')->references('id')->on('ingredient_categories');
            $table->decimal('density_g_per_ml', 10, 6)->nullable();
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
