<?php

namespace App\Http\Controllers;

use App\Models\BodyWeightRecord;
use App\Models\IngredientIntakeRecord;
use App\Models\MealIntakeRecord;
use App\Models\FoodListIntakeRecord;
use App\Models\Ingredient;
use App\Models\Meal;
use App\Models\FoodList;
use App\Models\Unit;
use App\Models\IntakeGuideline;
use App\Models\NutrientCategory;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DataController extends Controller
{
    /**
     * Show the overview page for trends in logged data
     */
    public function index()
    {
        $user = Auth::user();
        $userId = $user ? $user->id : null;
        return Inertia::render('Data/Index', [
            'body_weight_records' => BodyWeightRecord::getForUser($userId),
            'ingredient_intake_records' => IngredientIntakeRecord::getForUser($userId),
            'meal_intake_records' => MealIntakeRecord::getForUser($userId),
            'food_list_intake_records' => FoodListIntakeRecord::getForUser($userId),
            'user_ingredients' => Ingredient::getForUserWithCategoryAndUnits($userId),
            'meals' => Meal::getForUserWithUnit($userId),
            'food_lists' => FoodList::getForUserWithUnit($userId),
            'units' => Unit::getMassAndVolume(),
            'intake_guidelines' => IntakeGuideline::getForUser($userId),
            'nutrient_categories' => NutrientCategory::getWithName(),
        ]);
    }
}
