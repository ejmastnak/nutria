<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Meal;
use App\Models\FoodList;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LogController extends Controller
{
    /**
     * Show the page for logging data
     */
    public function index()
    {
        $user = Auth::user();
        $userId = $user ? $user->id : null;
        return Inertia::render('Log/Index', [
            'user_ingredients' => Ingredient::getForUserWithCategoryAndUnits($userId),
            'meals' => Meal::getForUserWithUnit($userId),
            'food_lists' => FoodList::getForUserWithUnit($userId),
            'units' => Unit::getMassAndVolume(),
        ]);
    }
}
