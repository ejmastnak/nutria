<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\FoodListController;
use App\Http\Controllers\IntakeGuidelineController;
use App\Models\Ingredient;
use App\Models\Meal;
use App\Models\FoodList;
use App\Models\IntakeGuideline;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

// These routes are available to unauthenticated users
Route::get('ingredients', [IngredientController::class, 'index'])->name('ingredients.index');
Route::get('intake-guidelines', [IntakeGuidelineController::class, 'index'])->name('intake-guidelines.index');

// User profiles
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
        // Ingredients
        Route::get('ingredients/create', [IngredientController::class, 'create'])->name('ingredients.create')->can('create', Ingredient::class);
        Route::get('ingredients/{ingredient}/clone', [IngredientController::class, 'clone'])->name('ingredients.clone')->can('create', Ingredient::class);
        Route::post('ingredients', [IngredientController::class, 'store'])->name('ingredients.store')->can('create', Ingredient::class);
        Route::get('ingredients/{ingredient}/edit', [IngredientController::class, 'edit'])->name('ingredients.edit')->can('update', 'ingredient');
        Route::put('ingredients/{ingredient}', [IngredientController::class, 'update'])->name('ingredients.update')->can('update', 'ingredient');
        Route::delete('ingredients/{ingredient}', [IngredientController::class, 'destroy'])->name('ingredients.destroy')->can('delete', 'ingredient');

        // Meals
        Route::get('meals', [MealController::class, 'index'])->name('meals.index')->can('viewAny', Meal::class);
        Route::get('meals/create', [MealController::class, 'create'])->name('meals.create')->can('create', Meal::class);
        Route::get('meals/{meal}/clone', [MealController::class, 'clone'])->name('meals.clone')->can('create', Meal::class);
        Route::post('meals', [MealController::class, 'store'])->name('meals.store')->can('create', Meal::class);
        Route::get('meals/{meal}/edit', [MealController::class, 'edit'])->name('meals.edit')->can('update', 'meal');
        Route::put('meals/{meal}', [MealController::class, 'update'])->name('meals.update')->can('update', 'meal');
        Route::delete('meals/{meal}', [MealController::class, 'destroy'])->name('meals.destroy')->can('delete', 'meal');
        Route::get('meals/{meal}', [MealController::class, 'show'])->name('meals.show')->can('view', 'meal');
        Route::put('meals/{meal}/save-as-ingredient', [MealController::class, 'saveAsIngredient'])->name('meals.save-as-ingredient')->can('saveAsIngredient', 'meal');

        // Food Lists
        Route::get('food-lists', [FoodListController::class, 'index'])->name('food-lists.index')->can('viewAny', FoodList::class);
        Route::get('food-lists/create', [FoodListController::class, 'create'])->name('food-lists.create')->can('create', FoodList::class);
        Route::get('food-lists/{food_list}/clone', [FoodListController::class, 'clone'])->name('food-lists.clone')->can('create', FoodList::class);
        Route::post('food-lists', [FoodListController::class, 'store'])->name('food-lists.store')->can('create', FoodList::class);
        Route::get('food-lists/{food_list}/edit', [FoodListController::class, 'edit'])->name('food-lists.edit')->can('update', 'food_list');
        Route::put('food-lists/{food_list}', [FoodListController::class, 'update'])->name('food-lists.update')->can('update', 'food_list');
        Route::delete('food-lists/{food_list}', [FoodListController::class, 'destroy'])->name('food-lists.destroy')->can('delete', 'food_list');
        Route::get('food-lists/{food_list}', [FoodListController::class, 'show'])->name('food-lists.show')->can('view', 'food_list');

        // Intake Guidelines
        Route::get('intake-guidelines/create', [IntakeGuidelineController::class, 'create'])->name('intake-guidelines.create')->('create', IntakeGuideline::class);
        Route::get('intake-guidelines/{intake_guideline}/clone', [IntakeGuidelineController::class, 'clone'])->name('intake-guidelines.clone')->('create', IntakeGuideline::class);
        Route::post('intake-guidelines', [IntakeGuidelineController::class, 'store'])->name('intake-guidelines.store')->('create', IntakeGuideline::class);
        Route::get('intake-guidelines/{intake_guideline}/edit', [IntakeGuidelineController::class, 'edit'])->name('intake-guidelines.edit')->('update', 'intake_guideline');
        Route::put('intake-guidelines/{intake_guideline}', [IntakeGuidelineController::class, 'update'])->name('intake-guidelines.update')->('update', 'intake_guideline');
        Route::delete('intake-guidelines/{intake_guideline}', [IntakeGuidelineController::class, 'destroy'])->name('intake-guidelines.destroy')->('delete', 'intake_guideline');

});

// These routes are available to unauthenticated users.
// The routes are intentionally defined last among the resource routes, since
// e.g. the catch-all parameter `ingredients/{ingredients}` would otherwise
// "capture" e.g. ingredients/create and ingredients/export
Route::get('ingredients/{ingredient}', [IngredientController::class, 'show'])->name('ingredients.show');
Route::get('intake-guidelines/{intake_guideline}', [IntakeGuidelineController::class, 'show'])->name('intake-guidelines.show');

require __DIR__.'/auth.php';
