<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nutrient extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'nutrients';

    public static function getWithUnit() {
        return self::with('unit:id,name')
            ->orderBy('seq_num')
            ->get([
                'id',
                'display_name',
                'unit_id',
                'unit',
                'nutrient_category_id',
                'precision',
                'seq_num',
            ]);
    }

    public function unit() {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function nutrient_category() {
        return $this->belongsTo(NutrientCategory::class, 'nutrient_category_id', 'id');
    }
}
