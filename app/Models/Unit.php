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
        'full_name',
        'to_grams',
    ];

    public static function numberMassAndVolumeUnits() {
        return self::whereNotNull('g')->orWhereNotNull('ml')->count();
    }

}
