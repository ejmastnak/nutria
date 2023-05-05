<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntakeGuidelineNutrient extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['intake_guideline_id', 'nutrient_id', 'rdi'];

    public function intake_guideline() {
        return $this->belongsTo(IntakeGuideline::class, 'intake_guideline_id', 'id');
    }

    public function nutrient() {
        return $this->belongsTo(Nutrient::class, 'nutrient_id', 'id');
    }
}
