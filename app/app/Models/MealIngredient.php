<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealIngredient extends Model
{
    use HasFactory;
    public $timestamps = false;

    public function meal() {
        return $this->belongsTo(Meal::class, 'meal_id', 'id');
    }

    public function unit() {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }
}
