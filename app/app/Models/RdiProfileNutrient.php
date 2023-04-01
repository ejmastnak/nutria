<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RdiProfileNutrient extends Model
{
    use HasFactory;
    public $timestamps = false;

    public function rdi_profile() {
        return $this->belongsTo(RdiProfile::class, 'rdi_profile_id', 'id');
    }

    public function nutrient() {
        return $this->belongsTo(Nutrient::class, 'nutrient_id', 'id');
    }
}
