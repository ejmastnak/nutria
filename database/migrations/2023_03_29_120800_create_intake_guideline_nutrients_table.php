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
        Schema::create('intake_guideline_nutrients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('intake_guideline_id')->references('id')->on('intake_guidelines')->cascadeOnDelete();
            $table->foreignId('nutrient_id')->references('id')->on('nutrients');
            $table->decimal('rdi', 10, 4);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intake_guideline_nutrients');
    }
};
