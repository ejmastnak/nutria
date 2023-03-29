<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;
    public $timestamps = false;

    public function meal_ingredients() {
        return $this->hasMany(MealIngredient::class, 'meal_ingredient_id', 'id');
    }
}
