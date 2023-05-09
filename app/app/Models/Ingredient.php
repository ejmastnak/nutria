<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'fdc_id', 'ingredient_category_id', 'density_g_per_ml', 'meal_id', 'user_id'];

    public function meal() {
        return $this->belongsTo(Meal::class, 'meal_id', 'id');
    }

    public function ingredient_category() {
        return $this->belongsTo(IngredientCategory::class, 'ingredient_category_id', 'id');
    }

    public function ingredient_nutrients() {
        return $this->hasMany(IngredientNutrient::class, 'ingredient_id', 'id');
    }
}
