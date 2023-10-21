<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntakeGuideline extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'user_id',
    ];

    public static function getForUser(?int $userId) {
        return self::where('user_id', null)
            ->orWhere('user_id', $userId)
            ->get(['id', 'name']);
    }

    public function intakeGuidelineNutrients() {
        return $this->hasMany(IntakeGuidelineNutrient::class, 'intake_guideline_id', 'id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
