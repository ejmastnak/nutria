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
        Schema::create('body_weight_records', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 10, 4);
            $table->foreignId('unit_id')->references('id')->on('units');
            $table->decimal('kg', 10, 4);
            $table->decimal('lb', 10, 4);
            $table->dateTime('date_time_utc');
            $table->foreignId('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('body_weight_records');
    }
};
