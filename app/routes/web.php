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
    Route::get('ingredients/create', [IngredientController::class, 'create'])->name('ingredients.create')->can('create', Ingredient::class);
    Route::post('ingredients', [IngredientController::class, 'store'])->name('ingredients.store')->can('create', Ingredient::class);
    Route::get('ingredients/{ingredient}/edit', [IngredientController::class, 'edit'])->name('ingredients.edit')->can('update', 'ingredient');
    Route::put('ingredients/{ingredient}', [IngredientController::class, 'update'])->name('ingredients.update')->can('update', 'ingredient');
    Route::delete('ingredients/{ingredient}', [IngredientController::class, 'destroy'])->name('ingredients.destroy')->can('delete', 'ingredient');

    // Meals
    Route::get('meals', [MealController::class, 'index'])->name('meals.index')->can('viewAll', Meal::class);
    Route::get('meals/create', [MealController::class, 'create'])->name('meals.create')->can('create', Meal::class);
    Route::post('meals', [MealController::class, 'store'])->name('meals.store')->can('create', Meal::class);
    Route::get('meals/{meal}/edit', [MealController::class, 'edit'])->name('meals.edit')->can('update', 'meal');
    Route::put('meals/{meal}', [MealController::class, 'update'])->name('meals.update')->can('update', 'meal');
    Route::delete('meals/{meal}', [MealController::class, 'destroy'])->name('meals.destroy')->can('delete', 'meal');
    Route::get('meals/{meal}', [MealController::class, 'show'])->name('meals.show')->can('view', 'meal');

    // Food lists
    Route::get('food-lists', [FoodListController::class, 'index'])->name('food-lists.index')->can('viewAll', FoodList::class);
    Route::get('food-lists/create', [FoodListController::class, 'create'])->name('food-lists.create')->can('create', FoodList::class);
    Route::post('food-lists', [FoodListController::class, 'store'])->name('food-lists.store')->can('create', FoodList::class);
    Route::get('food-lists/{meal}/edit', [FoodListController::class, 'edit'])->name('food-lists.edit')->can('update', 'meal');
    Route::put('food-lists/{meal}', [FoodListController::class, 'update'])->name('food-lists.update')->can('update', 'meal');
    Route::delete('food-lists/{meal}', [FoodListController::class, 'destroy'])->name('food-lists.destroy')->can('delete', 'meal');
    Route::get('food-lists/{meal}', [FoodListController::class, 'show'])->name('food-lists.show')->can('view', 'meal');

    // RDI Profiles
    Route::get('rdi-profiles/create', [RdiProfileController::class, 'create'])->name('rdi-profiles.create')->can('create', RdiProfile::class);
    Route::post('rdi-profiles', [RdiProfileController::class, 'store'])->name('rdi-profiles.store')->can('create', RdiProfile::class);
    Route::get('rdi-profiles/{meal}/edit', [RdiProfileController::class, 'edit'])->name('rdi-profiles.edit')->can('update', 'meal');
    Route::put('rdi-profiles/{meal}', [RdiProfileController::class, 'update'])->name('rdi-profiles.update')->can('update', 'meal');
    Route::delete('rdi-profiles/{meal}', [RdiProfileController::class, 'destroy'])->name('rdi-profiles.destroy')->can('delete', 'meal');

});

// These routes are available to unauthenticated users.
// The routes are intentionally defined last among the ingredients routes, since
// e.g. the catch-all parameter `ingredients/{ingredients}` would otherwise
// "capture" e.g. ingredients/create and ingredients/export
Route::get('ingredients/{ingredient}', [IngredientController::class, 'show'])->name('ingredients.show')->can('view', 'ingredient');
Route::get('rdi-profiles/{rdi_profile}', [RdiProfileController::class, 'show'])->name('rdi-profiles.show')->can('view', 'rdi_profile');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';
