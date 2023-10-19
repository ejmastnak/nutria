<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NutrientCategory extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['name'];

    public function unit() {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function nutrientCategory() {
        return $this->belongsTo(NutrientCategory::class, 'nutrient_category_id', 'id');
    }

}
