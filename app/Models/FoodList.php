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
        'user_id'
    ];

    public function food_list_ingredients() {
        return $this->hasMany(FoodListIngredient::class, 'food_list_id', 'id');
    }

    public function food_list_meals() {
        return $this->hasMany(FoodListMeal::class, 'food_list_id', 'id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
