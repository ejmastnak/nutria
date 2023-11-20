<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class IntakeGuidelineNutrient extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'intake_guideline_id',
        'nutrient_id',
        'rdi',
    ];

    protected function rdi(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value + 0,
        );
    }

    public function intake_guideline() {
        return $this->belongsTo(IntakeGuideline::class, 'intake_guideline_id', 'id');
    }

    public function nutrient() {
        return $this->belongsTo(Nutrient::class, 'nutrient_id', 'id');
    }

}
