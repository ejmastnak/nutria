<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nutrient extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'nutrients';

    public function unit() {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function nutrient_category() {
        return $this->belongsTo(NutrientCategory::class, 'nutrient_category_id', 'id');
    }
}
