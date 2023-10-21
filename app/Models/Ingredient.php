<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;
    protected $fillable = [
        'fdc_id',
        'name',
        'ingredient_category_id',
        'density_mass_unit_id',
        'density_mass_amount',
        'density_volume_unit_id',
        'density_volume_amount',
        'density_g_ml',
        'meal_id',
        'user_id',
    ];

    public function meal() {
        return $this->belongsTo(Meal::class, 'meal_id', 'id');
    }

    public function ingredientCategory() {
        return $this->belongsTo(IngredientCategory::class, 'ingredient_category_id', 'id');
    }

    public function ingredientNutrients() {
        return $this->hasMany(IngredientNutrient::class, 'ingredient_id', 'id');
    }

    public function customUnits() {
        return $this->hasMany(Unit::class, 'ingredient_id', 'id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function mealIngredients() {
        return $this->hasMany(MealIngredient::class, 'ingredient_id', 'id');
    }

    public function meals() {
        return $this->belongsToMany(Meal::class, 'meal_ingredients', 'ingredient_id', 'meal_id');
    }

    public function foodListIngredients() {
        return $this->hasMany(FoodListIngredient::class, 'ingredient_id', 'id');
    }

    public function foodLists() {
        return $this->belongsToMany(FoodList::class, 'food_list_ingredients', 'ingredient_id', 'food_list_id');
    }

}
