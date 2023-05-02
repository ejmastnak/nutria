<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RdiProfile extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'user_id'];

    public function rdi_profile_nutrients() {
        return $this->hasMany(RdiProfileNutrient::class, 'rdi_profile_id', 'id');
    }
}
