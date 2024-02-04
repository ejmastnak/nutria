<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntakeGuideline extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'priority',
        'description',
        'user_id',
    ];

    public function withNutrients() {
        // The long query is to ensure intake_guideline_nutrients are ordered by
        // nutrients.seq_num
        $this->load([
            'intake_guideline_nutrients' => function($query) {
                $query->select([
                    'intake_guideline_nutrients.id',
                    'intake_guideline_nutrients.intake_guideline_id',
                    'intake_guideline_nutrients.nutrient_id',
                    'intake_guideline_nutrients.rdi'
                ])
                ->join('nutrients', 'intake_guideline_nutrients.nutrient_id', '=', 'nutrients.id')
                ->orderBy('nutrients.seq_num');
            },
            'intake_guideline_nutrients.nutrient:id,display_name,unit_id,nutrient_category_id,precision,seq_num',
            'intake_guideline_nutrients.nutrient.unit:id,name'
        ]);
        return $this->only([
            'id',
            'name',
            'priority',
            'intake_guideline_nutrients'
        ]);
    }

    public static function getForUser(?int $userId) {
        return self::where('user_id', null)
            ->orWhere('user_id', $userId)
            ->orderByRaw('COALESCE(priority, 0) DESC')
            ->get(['id', 'name']);
    }

    /**
     *  Permissions for Index page, which shows built-in and user guidelines on
     *  the same page. Default guideline cannot be edited, but the user's can.
     */
    public static function getForUserWithPermissions(?User $user) {
        $userId = $user ? $user->id : null;
        return self::where('user_id', null)
            ->orWhere('user_id', $userId)
            ->orderByRaw('COALESCE(priority, 0) DESC')
            ->get(['id', 'name', 'priority'])
            ->map(fn($intakeGuideline) => [
                'id' => $intakeGuideline->id,
                'name' => $intakeGuideline->name,
                'priority' => $intakeGuideline->priority,
                'can_update' => $user ? $user->can('update', $intakeGuideline) : false,
                'can_delete' => $user ? $user->can('delete', $intakeGuideline) : false,
            ]);
    }

    /**
     *  Returns ids of all intake guidelines applicable to the inputted user.
     */
    public static function getIdsForUser(?int $userId) {
        return self::where('user_id', null)
        ->orWhere('user_id', $userId)
        ->orderByRaw('COALESCE(priority, 0) DESC')
        ->pluck('id');
    }

    public function intake_guideline_nutrients() {
        return $this->hasMany(IntakeGuidelineNutrient::class, 'intake_guideline_id', 'id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
