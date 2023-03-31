<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use Illuminate\Http\Request;
use Inertia\Intertia;

class MealController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Meals/Index', [
          'meals' => Meal::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Meals/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Meal $meal)
    {
        // Load name, amount, and unit (along with necessary intermediate
        // relationship) of each meal ingredient
        $meal->load(['meal_ingredients:meal_id,ingredient_id,amount,unit_id']);
        return Inertia::render('Meals/Show', [
            'meal' => $meal,
            'nutrient_profile' => NutrientProfileController::profileMeal($meal)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Meal $meal)
    {
        // Load name, amount, and unit (along with necessary intermediate
        // relationship) of each meal ingredient
        $meal->load(['meal_ingredients:meal_id,ingredient_id,amount,unit_id']);
        return Inertia::render('Meals/Edit', [
          'meal' => $meal
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Meal $meal)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Meal $meal)
    {
        //
    }
}
