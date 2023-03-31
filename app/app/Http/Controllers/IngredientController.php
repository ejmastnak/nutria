<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IngredientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Ingredients/Index', [
            'ingredients' => Ingredient::all(['id', 'name', 'ingredient_category_id']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Ingredients/Create');
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
    public function show(Ingredient $ingredient)
    {
        return Inertia::render('Ingredients/Show', [
            'ingredient' => $ingredient->only(['id', 'name', 'ingredient_category_id', 'density_g_per_ml']),
            'nutrient_profile' => NutrientProfileController::profileIngredient($ingredient)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ingredient $ingredient)
    {
        // Load name, amount per 100 g of ingredient, and unit (along with
        // necessary intermediate relationship) of each ingredient nutrient
        $ingredient->load(['ingredient_nutrients:id,ingredient_id,nutrient_id,amount_per_100g', 'ingredient_nutrients.nutrient:id,display_name,unit_id', 'ingredient_nutrients.nutrient.unit:id,name']);
        return Inertia::render('Ingredients/Edit', [
            'ingredient' => $ingredient->only(['id', 'name', 'ingredient_category_id', 'density_g_per_ml', 'ingredient_nutrients']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ingredient $ingredient)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ingredient $ingredient)
    {
        //
    }
}
