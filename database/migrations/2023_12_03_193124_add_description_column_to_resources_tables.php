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
        Schema::table('ingredients', function (Blueprint $table) {
            $table->string('description')->nullable();
        });
        Schema::table('meals', function (Blueprint $table) {
            $table->string('description')->nullable();
        });
        Schema::table('food_lists', function (Blueprint $table) {
            $table->string('description')->nullable();
        });
        Schema::table('intake_guidelines', function (Blueprint $table) {
            $table->string('description')->nullable();
        });
        Schema::table('body_weight_records', function (Blueprint $table) {
            $table->string('description')->nullable();
        });
        Schema::table('ingredient_intake_records', function (Blueprint $table) {
            $table->string('description')->nullable();
        });
        Schema::table('meal_intake_records', function (Blueprint $table) {
            $table->string('description')->nullable();
        });
        Schema::table('food_list_intake_records', function (Blueprint $table) {
            $table->string('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropColumn('description');
        });
        Schema::table('meals', function (Blueprint $table) {
            $table->dropColumn('description');
        });
        Schema::table('food_lists', function (Blueprint $table) {
            $table->dropColumn('description');
        });
        Schema::table('intake_guidelines', function (Blueprint $table) {
            $table->dropColumn('description');
        });
        Schema::table('body_weight_records', function (Blueprint $table) {
            $table->dropColumn('description');
        });
        Schema::table('ingredient_intake_records', function (Blueprint $table) {
            $table->dropColumn('description');
        });
        Schema::table('meal_intake_records', function (Blueprint $table) {
            $table->dropColumn('description');
        });
        Schema::table('food_list_intake_records', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
