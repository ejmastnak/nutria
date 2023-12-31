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
            $table->foreignId('unit_id')->references('id')->on('units');
            $table->foreignId('nutrient_category_id')->references('id')->on('nutrient_categories');
            $table->integer('precision')->default(2);
            $table->integer('seq_num');
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
