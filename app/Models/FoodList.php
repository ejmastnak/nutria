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
