<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class FoodIntakeRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'ingredient_id',
        'meal_id',
        'amount',
        'unit_id',
        'mass_in_grams',
        'date_time_utc',
        'user_id',
        'description',
        'created_at',
        'updated_at',
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
                'unit:id,name,g,ml,seq_num,ingredient_id,meal_id,custom_grams',
                'ingredient:id,name,density_g_ml',
                'meal:id,name',
                'ingredient.custom_units:id,name,g,ml,seq_num,ingredient_id,custom_grams',
                'meal.meal_unit:id,name,g,ml,seq_num,meal_id,custom_grams',
            ])
            ->orderBy('date_time_utc', 'desc')
            ->take(15)
            ->get(['id', 'ingredient_id', 'meal_id', 'amount', 'unit_id', 'date_time_utc']);
    }

    public static function getForUserPaginated(?int $userId) {
        return is_null($userId) ? [] : self::where('user_id', $userId)
            ->with([
                'unit:id,name,g,ml,seq_num,ingredient_id,meal_id,custom_grams',
                'ingredient:id,name,density_g_ml',
                'meal:id,name',
                'ingredient.custom_units:id,name,g,ml,seq_num,ingredient_id,custom_grams',
                'meal.meal_unit:id,name,g,ml,seq_num,meal_id,custom_grams',
            ])
            ->orderBy('date_time_utc', 'desc')
            ->paginate(3, ['id', 'ingredient_id', 'meal_id', 'amount', 'unit_id', 'date_time_utc']);
    }

    public function ingredient() {
        return $this->belongsTo(Ingredient::class, 'ingredient_id', 'id');
    }

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
