<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'mass_in_grams',
        'user_id',
    ];

    public function ingredient() {
        return $this->hasOne(Ingredient::class, 'meal_id', 'id');
    }

    public function mealIngredients() {
        return $this->hasMany(MealIngredient::class, 'meal_id', 'id')->orderBy('seq_num');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function customUnits() {
        return $this->hasMany(Unit::class, 'meal_id', 'id');
    }

    public function foodListMeals() {
        return $this->hasMany(FoodListMeal::class, 'meal_id', 'id');
    }

    public function foodLists() {
        return $this->belongsToMany(FoodList::class, 'food_list_meals', 'meal_id', 'food_list_id');
    }

}
