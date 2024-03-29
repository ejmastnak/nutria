<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Http\Request;

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
        'description',
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

    public static function getForUserPaginated(?int $userId, Request $request) {
        return is_null($userId) ? [] : self::where('user_id', $userId)
            ->with([
                'unit:id,name,g,ml,seq_num,ingredient_id,meal_id,custom_grams',
                'ingredient:id,name,density_g_ml',
                'ingredient.custom_units:id,name,g,ml,seq_num,ingredient_id,custom_grams',
                'meal:id,name',
                'meal.meal_unit:id,name,g,ml,seq_num,meal_id,custom_grams',
            ])
            ->filter($request->only(['food_intake_records_from_date_time_utc', 'food_intake_records_to_date_time_utc']))
            ->orderBy('date_time_utc', 'desc')
            ->paginate(config('pagination.food_intake_records'))
            ->withQueryString()
            ->through(fn ($foodIntakeRecord) => [
                'id' => $foodIntakeRecord->id,
                'ingredient_id' => $foodIntakeRecord->ingredient_id,
                'ingredient' => $foodIntakeRecord->ingredient,
                'meal_id' => $foodIntakeRecord->meal_id,
                'meal' => $foodIntakeRecord->meal,
                'amount' => $foodIntakeRecord->amount,
                'unit_id' => $foodIntakeRecord->unit_id,
                'unit' => $foodIntakeRecord->unit,
                'date_time_utc' => $foodIntakeRecord->date_time_utc,
                'description' => $foodIntakeRecord->description,
            ]);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['food_intake_records_from_date_time_utc'] ?? null, function ($query, $fromDateTimeUtc) {
            $query->where('date_time_utc', '>=', $fromDateTimeUtc);
        })->when($filters['food_intake_records_to_date_time_utc'] ?? null, function ($query, $toDateTimeUtc) {
            $query->where('date_time_utc', '<=', $toDateTimeUtc);
        });
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
