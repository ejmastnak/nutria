<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodListIntakeRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'food_list_id',
        'amount',
        'unit_id',
        'mass_in_grams',
        'date',
        'time',
        'user_id',
    ];

    public function food_list() {
        return $this->belongsTo(FoodList::class, 'food_list_id', 'id');
    }

    public function unit() {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
