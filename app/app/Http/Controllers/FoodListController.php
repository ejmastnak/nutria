<?php

namespace App\Http\Controllers;

use App\Models\FoodList;
use Illuminate\Http\Request;
use Inertia\Intertia;

class FoodListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('FoodLists/Index', [
          'foodLists' => FoodList::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('FoodLists/Create');
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
    public function show(FoodList $foodList)
    {
        // Load name, amount, and unit (along with necessary intermediate
        // relationships) of each food list ingredient and food list meal.
        $foodList->load([
            'food_list_ingredients:id,ingredient_id,food_list_id,amount,unit_id',
            'food_list_ingredients.ingredient:id,name',
            'food_list_ingredients.unit:id,name',
            'food_list_meals:id,meal_id,food_list_id,amount,unit_id',
            'food_list_meals.meal:id,name',
            'food_list_meals.unit:id,name'
        ]);
        return Inertia::render('FoodLists/Show', [
            'foodLists' => FoodList::all(),
            'nutrient_profile' => NutrientProfileController::profileFoodList($foodList)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FoodList $foodList)
    {
        // Load name, amount, and unit (along with necessary intermediate
        // relationships) of each food list ingredient and food list meal.
        $foodList->load([
            'food_list_ingredients:id,ingredient_id,food_list_id,amount,unit_id',
            'food_list_ingredients.ingredient:id,name',
            'food_list_ingredients.unit:id,name',
            'food_list_meals:id,meal_id,food_list_id,amount,unit_id',
            'food_list_meals.meal:id,name',
            'food_list_meals.unit:id,name'
        ]);
        return Inertia::render('FoodLists/Show', [
            'foodLists' => FoodList::all(),
            'nutrient_profile' => NutrientProfileController::profileFoodList($foodList)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FoodList $foodList)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FoodList $foodList)
    {
        //
    }
}
