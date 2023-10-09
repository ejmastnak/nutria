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
        Schema::create('nutrients', function (Blueprint $table) {
            // Uses hard-coded FDC nutrient IDs as primary key
            $table->unsignedBigInteger('id');
            $table->primary('id');
            $table->string('name');
            $table->string('display_name');
            $table->unsignedBigInteger('unit_id');
            $table->foreign('unit_id')->references('id')->on('units');
            $table->unsignedBigInteger('nutrient_category_id');
            $table->foreign('nutrient_category_id')->references('id')->on('nutrient_categories')->cascadeOnDelete();
            $table->integer('precision')->default(2);
            $table->integer('display_order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nutrients');
    }
};