<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    protected function amount(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value + 0,
        );
    }

    public static function getForUser(?int $userId) {
        return is_null($userId) ? [] : self::where('user_id', $userId)
            ->with([
                'unit:id,name,g,ml,seq_num,meal_id,custom_grams',
                'meal:id,name',
                'meal.meal_unit:id,name,g,ml,seq_num,meal_id,custom_grams',
            ])
            ->orderBy('date')
            ->orderBy('time')
            ->get(['id', 'meal_id', 'amount', 'unit_id', 'date', 'time']);
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
