<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\FoodListController;
use App\Http\Controllers\RdiProfileController;
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
Route::get('rdi-profiles', [RdiProfileController::class, 'index'])->name('rdi-profiles.index');

Route::middleware('auth')->group(function () {
    // User profiles
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Ingredients
    Route::get('ingredients/create', [IngredientController::class, 'create'])->name('ingredients.create');
    Route::post('ingredients', [IngredientController::class, 'store'])->name('ingredients.store');
    Route::get('ingredients/{ingredient}/edit', [IngredientController::class, 'edit'])->name('ingredients.edit');
    Route::put('ingredients/{ingredient}', [IngredientController::class, 'update'])->name('ingredients.update');
    Route::delete('ingredients/{ingredient}', [IngredientController::class, 'destroy'])->name('ingredients.destroy');

    // Meals
    Route::get('meals', [MealController::class, 'index'])->name('meals.index');
    Route::get('meals/create', [MealController::class, 'create'])->name('meals.create');
    Route::post('meals', [MealController::class, 'store'])->name('meals.store');
    Route::get('meals/{meal}/edit', [MealController::class, 'edit'])->name('meals.edit');
    Route::put('meals/{meal}', [MealController::class, 'update'])->name('meals.update');
    Route::delete('meals/{meal}', [MealController::class, 'destroy'])->name('meals.destroy');
    Route::get('meals/{meal}', [MealController::class, 'show'])->name('meals.show');

    // Food lists
    Route::get('food-lists', [FoodListController::class, 'index'])->name('food-lists.index');
    Route::get('food-lists/create', [FoodListController::class, 'create'])->name('food-lists.create');
    Route::post('food-lists', [FoodListController::class, 'store'])->name('food-lists.store');
    Route::get('food-lists/{food_list}/edit', [FoodListController::class, 'edit'])->name('food-lists.edit');
    Route::put('food-lists/{food_list}', [FoodListController::class, 'update'])->name('food-lists.update');
    Route::delete('food-lists/{food_list}', [FoodListController::class, 'destroy'])->name('food-lists.destroy');
    Route::get('food-lists/{food_list}', [FoodListController::class, 'show'])->name('food-lists.show');

    // RDI Profiles
    Route::get('rdi-profiles/create', [RdiProfileController::class, 'create'])->name('rdi-profiles.create');
    Route::post('rdi-profiles', [RdiProfileController::class, 'store'])->name('rdi-profiles.store');
    Route::get('rdi-profiles/{rdi_profile}/edit', [RdiProfileController::class, 'edit'])->name('rdi-profiles.edit');
    Route::put('rdi-profiles/{rdi_profile}', [RdiProfileController::class, 'update'])->name('rdi-profiles.update');
    Route::delete('rdi-profiles/{rdi_profile}', [RdiProfileController::class, 'destroy'])->name('rdi-profiles.destroy');

});

// These routes are available to unauthenticated users.
// The routes are intentionally defined last among the ingredients routes, since
// e.g. the catch-all parameter `ingredients/{ingredients}` would otherwise
// "capture" e.g. ingredients/create and ingredients/export
Route::get('ingredients/{ingredient}', [IngredientController::class, 'show'])->name('ingredients.show');
Route::get('rdi-profiles/{rdi_profile}', [RdiProfileController::class, 'show'])->name('rdi-profiles.show');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';
