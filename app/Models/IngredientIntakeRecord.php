<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class IngredientIntakeRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'ingredient_id',
        'amount',
        'unit_id',
        'mass_in_grams',
        'date_time_utc',
        'user_id',
    ];

    protected function amount(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value + 0,
        );
    }

    public static function getForUser(?int $userId) {
        return is_null($userId) ? [] : self::where('user_id', $userId)
            ->with([
                'unit:id,name,g,ml,seq_num,ingredient_id,custom_grams',
                'ingredient:id,name,density_g_ml',
                'ingredient.custom_units:id,name,g,ml,seq_num,ingredient_id,custom_grams',
            ])
            ->orderBy('date_time_utc', 'desc')
            ->take(15)
            ->get(['id', 'ingredient_id', 'amount', 'unit_id', 'date_time_utc']);
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
