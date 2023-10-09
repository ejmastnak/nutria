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
        'user_id'
    ];

    public function ingredient() {
        return $this->hasOne(Ingredient::class, 'meal_id', 'id');
    }

    public function meal_ingredients() {
        return $this->hasMany(MealIngredient::class, 'meal_id', 'id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}