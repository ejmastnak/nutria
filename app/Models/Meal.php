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

    public function withIngredientsAndChildIngredient() {
        $this->load([
            'mealIngredients:id,meal_id,ingredient_id,amount,unit_id',
            'mealIngredients.ingredient:id,name',
            'mealIngredients.ingredient.customUnits:id,name,g,ml,seq_num,ingredient_id,meal_id,custom_grams',
            'mealIngredients.unit:id,name,g,ml,seq_num,ingredient_id,meal_id,custom_grams',
            'ingredient:id,meal_id,name',
        ]);
        return $this->only([
            'id',
            'name',
            'mass_in_grams',
            'mealIngredients',
            'ingredient',
        ]);
    }

    public static function getForUser(?int $userId) {
        return is_null($userId) ? [] : self::where('user_id', $userId)
            ->with('ingredient:id,meal_id,name')
            ->get(['id', 'name']);
    }

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
