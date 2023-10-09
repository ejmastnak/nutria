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
        'meal_id',
        'user_id'
    ];

    public function meal() {
        return $this->belongsTo(Meal::class, 'meal_id', 'id');
    }

    public function ingredient_category() {
        return $this->belongsTo(IngredientCategory::class, 'ingredient_category_id', 'id');
    }

    public function ingredient_nutrients() {
        return $this->hasMany(IngredientNutrient::class, 'ingredient_id', 'id');
    }

    public function ingredient_nonmass_units() {
        return $this->hasMany(IngredientNonmassUnit::class, 'ingredient_id', 'id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
