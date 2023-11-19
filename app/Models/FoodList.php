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
            'food_list_ingredients:id,food_list_id,ingredient_id,amount,unit_id',
            'food_list_ingredients.ingredient:id,name',
            'food_list_ingredients.ingredient.custom_units:id,name,g,ml,seq_num,ingredient_id,meal_id,custom_grams',
            'food_list_ingredients.unit:id,name,g,ml,seq_num,ingredient_id,meal_id,custom_grams',
            'food_list_meals:id,food_list_id,meal_id,amount,unit_id',
            'food_list_meals.meal:id,name,mass_in_grams',
            'food_list_meals.meal.meal_unit:id,name,g,ml,seq_num,ingredient_id,meal_id,custom_grams',
            'food_list_meals.unit:id,name,g,ml,seq_num,ingredient_id,meal_id,custom_grams',
            'food_list_unit:id,name,g,ml,seq_num,food_list_id,custom_grams',
        ]);
        return $this->only([
            'id',
            'name',
            'mass_in_grams',
            'food_list_ingredients',
            'food_list_meals',
            'food_list_unit',
        ]);
    }

    public static function getForUser(?int $userId) {
        return is_null($userId) ? [] : self::where('user_id', $userId)
            ->get(['id', 'name']);
    }

    public function food_list_ingredients() {
        return $this->hasMany(FoodListIngredient::class, 'food_list_id', 'id')->orderBy('seq_num');
    }

    public function food_list_meals() {
        return $this->hasMany(FoodListMeal::class, 'food_list_id', 'id')->orderBy('seq_num');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function food_list_unit() {
        return $this->hasOne(Unit::class, 'food_list_id', 'id');
    }

}
