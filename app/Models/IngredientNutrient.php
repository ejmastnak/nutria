<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class IngredientNutrient extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'ingredient_id',
        'nutrient_id',
        'amount',
        'amount_per_100g',
    ];

    protected function amount(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value + 0,
        );
    }

    public function ingredient() {
        return $this->belongsTo(Ingredient::class, 'ingredient_id', 'id');
    }

    public function nutrient() {
        return $this->belongsTo(Nutrient::class, 'nutrient_id', 'id');
    }

    /**
     *  Returns the String name of the unit in which this nutrient is measured
     */
    public function unit() {
        return $this->belongsTo(Nutrient::class, 'nutrient_id', 'id')->first()->unit->name;
    }

}
