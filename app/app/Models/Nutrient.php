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
}
