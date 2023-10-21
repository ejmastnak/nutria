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
            ->orderBy('display_order_id')
            ->get([
                'id',
                'display_name',
                'unit_id',
                'nutrient_category_id',
                'precision',
                'display_order_id'
            ]);
    }

    public function unit() {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function nutrientCategory() {
        return $this->belongsTo(NutrientCategory::class, 'nutrient_category_id', 'id');
    }
}
