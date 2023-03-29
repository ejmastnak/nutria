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
        Schema::create('ingredient_categories', function (Blueprint $table) {
            // Uses hard-coded FDC ingredient category IDs as primary key
            $table->unsignedBigInteger('id');
            $table->primary('id');	
            $table->string('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredient_categories');
    }
};
