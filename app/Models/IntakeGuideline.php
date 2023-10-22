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

    public function withNutrients() {
        // The long query is to ensure intake_guideline_nutrients are ordered by
        // nutrients.display_order
        $this->load([
            'intakeGuidelineNutrients' => function($query) {
                $query->select([
                    'intake_guideline_nutrients.id',
                    'intake_guideline_nutrients.intake_guideline_id',
                    'intake_guideline_nutrients.nutrient_id',
                    'intake_guideline_nutrients.rdi'
                ])
                ->join('nutrients', 'intake_guideline_nutrients.nutrient_id', '=', 'nutrients.id')
                ->orderBy('nutrients.display_order');
            },
            'intake_guideline_nutrients.nutrient:id,display_name,unit_id,nutrient_category_id,precision,display_order',
            'intake_guideline_nutrients.nutrient.unit:id,name'
        ]);
        return $this->only(['id', 'name', 'intakeGuidelineNutrients']);
    }

    public static function getForUser(?int $userId) {
        return self::where('user_id', null)
            ->orWhere('user_id', $userId)
            ->get(['id', 'name']);
    }

    /**
     *  Permissions for Index page, which shows built-in and user guidelines on
     *  the same page. Default guideline cannot be edited, but the user's can.
     */
    public static function getForUserWithPermissions(?int $userId) {
        return self::where('user_id', null)
            ->orWhere('user_id', $userId)
            ->get(['id', 'name'])
            ->map(fn($intakeGuideline) => [
                'id' => $intakeGuideline->id,
                'name' => $intakeGuideline->name,
                'can_edit' => $user ? $user->can('update', $intakeGuideline) : false,
                'can_delete' => $user ? $user->can('delete', $intakeGuideline) : false,
            ]);
    }

    public function intakeGuidelineNutrients() {
        return $this->hasMany(IntakeGuidelineNutrient::class, 'intake_guideline_id', 'id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
