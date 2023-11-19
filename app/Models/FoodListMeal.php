<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class FoodListMeal extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'food_list_id',
        'meal_id',
        'amount',
        'unit_id',
        'mass_in_grams',
        'seq_num',
    ];

    protected function amount(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value + 0,
        );
    }

    public function food_list() {
        return $this->belongsTo(FoodList::class, 'food_list_id', 'id');
    }

    public function meal() {
        return $this->belongsTo(Meal::class, 'meal_id', 'id');
    }

    public function unit() {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

}
