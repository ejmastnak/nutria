<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\Nutrient;
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

    /**
     * Incoming request takes the form
     *
     * {
     *   "name": "Foo",
     *   "category_id": null,
     *   "density_g_per_ml": null,
     *   "nutrients": [
     *     {
     *       "nutrient_id": 0,
     *       "amount_per_100g": 0.0
     *     }
     *   ]
     * }
     *
     * Validation checks that:
     *
     * - name is a string with sane min and max length.
     * - category_id, is either null or present in ingredient_categories,id
     * - density_g_per_ml is either null or a positive float
     * - nutrients is an array and contains exactly one item for each record in nutrients table
     * - nutrients.*.nutrient_id is present in nutrients,id
     * - nutrients.*.amount_per_100g is a positive float
     *      
     */
    public function validateStoreOrUpdateRequest(Request $request) {
        $num_nutrients = Nutrient::count();
        $request->validate([
            'name' => ['required', 'min:1', 'max:500'],
            'category_id' => ['nullable', 'integer', 'in:ingredient_categories,id'],
            'density_g_per_ml' => ['nullable', 'numeric', 'gt:0'],
            'nutrients' => ['required', 'array', 'min:' . $num_nutrients, 'max:' . $num_nutrients,],
            'nutrients.*.nutrient_id' => ['required', 'distinct', 'integer', 'in:nutrients,id'],
            'nutrients.*.amount_per_100g' => ['required', 'numeric', 'gt:0'],
        ]);
    }
}
