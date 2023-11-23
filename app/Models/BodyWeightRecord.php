<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BodyWeightRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'unit_id',
        'kg',
        'lb',
        'date',
        'time',
        'user_id',
    ];

    public static function getForUser(?int $userId) {
        return is_null($userId) ? [] : self::where('user_id', $userId)
            ->with('unit:id,name')
            ->get(['id', 'amount', 'unit_id', 'date', 'time']);
    }

    public function unit() {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
