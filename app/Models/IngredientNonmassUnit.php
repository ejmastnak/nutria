<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngredientNonmassUnit extends Model
{
    use HasFactory;
    protected $timestamps = false;
    protected $fillable = [
        'ingredient_id',
        'unit_id',
        'to_grams',
    ];

    public function ingredient() {
        return $this->belongsTo(Ingredient::class, 'ingredient_id', 'id');
    }

    public function unit() {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

}
