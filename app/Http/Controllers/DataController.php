<?php

namespace App\Http\Controllers;

use App\Models\BodyWeightRecord;
use App\Models\FoodIntakeRecord;
use App\Models\Ingredient;
use App\Models\Meal;
use App\Models\Unit;
use App\Models\IntakeGuideline;
use App\Models\NutrientCategory;
use App\Http\Requests\NutrientProfileForDateRangeRequest;
use App\Services\NutrientProfileService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DataController extends Controller
{
    /**
     * Show the overview page for trends in logged data
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $userId = $user ? $user->id : null;
        return Inertia::render('Data/Index', [
            'body_weight_records_paginator' => BodyWeightRecord::getForUserPaginated($userId, $request),
            'food_intake_records_paginator' => FoodIntakeRecord::getForUserPaginated($userId, $request),
            'user_ingredients' => Ingredient::getForUserWithCategoryAndUnits($userId),
            'meals' => Meal::getForUserWithUnit($userId),
            'units' => Unit::getMassAndVolume(),
            'intake_guidelines' => IntakeGuideline::getForUser($userId),
            'nutrient_categories' => NutrientCategory::getWithName(),
        ]);
    }

    public function nutrientProfileForDateRange(NutrientProfileForDateRangeRequest $request, NutrientProfileService $nutrientProfileService) {
        return Response::json([
            'nutrient_profiles' => $nutrientProfileService->getNutrientProfilesForDateRange($request->validated(), $request->user()->id),
            'days_with_records' => $nutrientProfileService->getDaysWithFoodIntakeRecordsInDateRange($request->validated(), $request->user()->id),
        ]);
    }

}
