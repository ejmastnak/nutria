<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngredientIntakeRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'ingredient_id',
        'amount',
        'unit_id',
        'mass_in_grams',
        'date',
        'time',
        'user_id',
    ];

    public static function getForUser(?int $userId) {
        return is_null($userId) ? [] : self::where('user_id', $userId)
            ->with([
                'unit:id,name,g,ml,seq_num,ingredient_id,custom_grams',
                'ingredient:id,name,density_g_ml',
                'ingredient.custom_units:id,name,g,ml,seq_num,ingredient_id,custom_grams',
            ])
            ->get(['id', 'ingredient_id', 'amount', 'unit_id', 'date', 'time']);
    }

    public function ingredient() {
        return $this->belongsTo(Ingredient::class, 'ingredient_id', 'id');
    }

    public function unit() {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
