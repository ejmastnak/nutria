<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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

}
