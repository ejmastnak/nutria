<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealIntakeRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'meal_id',
        'amount',
        'unit_id',
        'mass_in_grams',
        'date',
        'time',
        'user_id',
    ];

    public function meal() {
        return $this->belongsTo(Meal::class, 'meal_id', 'id');
    }

    public function unit() {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
