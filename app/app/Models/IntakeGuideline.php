<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntakeGuideline extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'user_id'];

    public function intake_guideline_nutrients() {
        return $this->hasMany(IntakeGuidelineNutrient::class, 'intake_guideline_id', 'id');
    }
}
