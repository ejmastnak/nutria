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
        // relationships) of each meal ingredient
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

    /**
     * Incoming request takes the form
     *
     * {
     *   "name": "Foo",
     *   "ingredients": [
     *     {
     *       "ingredient_id": 0,
     *       "amount": 0.0,
     *       "unit_id": 0
     *     }
     *   ]
     * }
     *
     * Validation checks that:
     *
     * - name is a string with sane min and max length.
     * - ingredients is an array with at least one item and a sane maximum length
     * - ingredients.*.ingredient_id is present in ingredients,id
     * - ingredients.*.amount is a positive float
     * - ingredients.*.unit_id is present in units,id
     *      
     */
    public function validateStoreOrUpdateRequest(Request $request) {
        $num_nutrients = Nutrient::count();
        $request->validate([
            'name' => ['required', 'min:1', 'max:500'],
            'ingredients' => ['required', 'array', 'min:1', 'min:500'],
            'ingredients.*.ingredient_id' => ['required', 'integer', 'in:nutrients,id'],
            'ingredients.*.amount' => ['required', 'numeric', 'gt:0'],
            'ingredients.*.unit_id' => ['required', 'integer', 'in:units,id'],
        ]);
    }

}
