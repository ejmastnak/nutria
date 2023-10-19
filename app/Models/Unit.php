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
        'custom_unit_amount',
        'custom_mass_amount',
        'custom_mass_unit_id',
        'custom_grams',
    ];

    public static function numberMassAndVolumeUnits() {
        return self::whereNotNull('g')->orWhereNotNull('ml')->count();
    }

}
