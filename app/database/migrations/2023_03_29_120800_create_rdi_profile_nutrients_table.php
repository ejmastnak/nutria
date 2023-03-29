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
        Schema::create('rdi_profile_nutrients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rdi_profile_id');
            $table->foreign('rdi_profile_id')->references('id')->on('rdi_profiles')->cascadeOnDelete();
            $table->unsignedBigInteger('nutrient_id');
            $table->foreign('nutrient_id')->references('id')->on('nutrients');
            $table->decimal('rdi', 10, 3);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rdi_profile_nutrients');
    }
};
