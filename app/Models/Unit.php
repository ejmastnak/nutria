<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'name',
        'g',
        'ml',
        'seq_num',
        'ingredient_id',
        'meal_id',
        'custom_unit_amount',
        'custom_mass_amount',
        'custom_mass_unit_id',
        'custom_grams',
    ];

    protected function customMassAmount(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => is_null($value) ? null : $value + 0,
        );
    }

    protected function customUnitAmount(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => is_null($value) ? null : $value + 0,
        );
    }

    public static function getMassAndVolume() {
        return self::whereNotNull('g')
            ->orWhereNotNull('ml')
            ->get([
                'id',
                'name',
                'g',
                'ml',
                'seq_num',
            ]);
    }

    public static function numberMassAndVolumeUnits() {
        return self::whereNotNull('g')->orWhereNotNull('ml')->count();
    }

    public static function gramId() {
        return self::where('name', 'g')->first()->id;
    }

    public function ingredient() {
        return $this->belongsTo(Ingredient::class, 'ingredient_id', 'id');
    }

    public function custom_mass_unit() {
        return $this->belongsTo(Unit::class, 'custom_mass_unit_id', 'id');
    }

}
