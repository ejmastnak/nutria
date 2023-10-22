<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodList extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'mass_in_grams',
        'user_id',
    ];

    public function withIngredientsAndMeals() {
        $this->load([
            'foodListIngredients:id,food_list_id,ingredient_id,amount,unit_id',
            'foodListIngredients.ingredient:id,name',
            'foodListIngredients.ingredient.customUnits:id,name,g,ml,seq_num,ingredient_id,meal_id,custom_grams',
            'foodListIngredients.unit:id,name,g,ml,seq_num,ingredient_id,meal_id,custom_grams',
            'foodListMeals:id,food_list_id,meal_id,amount,unit_id',
            'foodListMeals.meal:id,name,mass_in_grams',
            'foodListMeals.meal.customUnits:id,name,g,ml,seq_num,ingredient_id,meal_id,custom_grams',
            'foodListMeals.unit:id,name,g,ml,seq_num,ingredient_id,meal_id,custom_grams',
        ]);
        return $this->only([
            'id',
            'name',
            'mass_in_grams',
            'foodListIngredients',
            'foodListMeals',
        ]);
    }

    public static function getForUser(?int $userId) {
        return is_null($userId) ? [] : self::where('user_id', $userId)
            ->get(['id', 'name']);
    }

    public function foodListIngredients() {
        return $this->hasMany(FoodListIngredient::class, 'food_list_id', 'id')->orderBy('seq_num');
    }

    public function foodListMeals() {
        return $this->hasMany(FoodListMeal::class, 'food_list_id', 'id')->orderBy('idx');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
