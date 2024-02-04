<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'mass_in_grams',
        'user_id',
    ];

    public function withIngredientsAndChildIngredient() {
        $this->load([
            'meal_ingredients:id,meal_id,ingredient_id,amount,unit_id',
            'meal_ingredients.ingredient:id,name',
            'meal_ingredients.ingredient.custom_units:id,name,g,ml,seq_num,ingredient_id,meal_id,custom_grams',
            'meal_ingredients.unit:id,name,g,ml,seq_num,ingredient_id,meal_id,custom_grams',
            'ingredient:id,meal_id,name',
            'meal_unit:id,name,g,ml,seq_num,meal_id,custom_grams',
        ]);
        return $this->only([
            'id',
            'name',
            'description',
            'mass_in_grams',
            'meal_ingredients',
            'ingredient',
            'meal_unit',
        ]);
    }

    public static function getForUser(?int $userId) {
        return is_null($userId) ? [] : self::where('user_id', $userId)
            ->get(['id', 'name']);
    }

    public static function getForUserWithChildIngredient(?int $userId) {
        return is_null($userId) ? [] : self::where('user_id', $userId)
            ->with('ingredient:id,meal_id,name')
            ->get(['id', 'name']);
    }

    public static function getForUserWithUnit(?int $userId) {
        return is_null($userId) ? [] : self::where('user_id', $userId)
            ->with('meal_unit:id,name,g,ml,seq_num,ingredient_id,meal_id,custom_grams')
            ->get(['id', 'name', 'mass_in_grams']);
    }

    public function ingredient() {
        return $this->hasOne(Ingredient::class, 'meal_id', 'id');
    }

    public function meal_ingredients() {
        return $this->hasMany(MealIngredient::class, 'meal_id', 'id')->orderBy('seq_num');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function meal_unit() {
        return $this->hasOne(Unit::class, 'meal_id', 'id');
    }

    public function food_list_meals() {
        return $this->hasMany(FoodListMeal::class, 'meal_id', 'id');
    }

    public function food_lists() {
        return $this->belongsToMany(FoodList::class, 'food_list_meals', 'meal_id', 'food_list_id');
    }

}
