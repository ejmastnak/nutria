<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\IngredientNutrient;
use App\Models\Nutrient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
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
        // Validate request
        $num_nutrients = Nutrient::count();
        $request->validate([
            'name' => ['required', 'min:1', 'max:500'],
            'category_id' => ['nullable', 'integer', 'in:ingredient_categories,id'],
            'density_g_per_ml' => ['nullable', 'numeric', 'gt:0'],
            'ingredient_nutrients' => ['required', 'array', 'min:' . $num_nutrients, 'max:' . $num_nutrients],
            'ingredient_nutrients.*.nutrient_id' => ['required', 'distinct', 'integer', 'in:nutrients,id'],
            'ingredient_nutrients.*.amount_per_100g' => ['required', 'numeric', 'gt:0'],
        ]);

        // Create ingredient
        $ingredient = Ingredient::create([
            'name' => $request->name,
            'fdc_id' => $request->fdc_id,
            'ingredient_category_id' => $request->ingredient_category_id,
            'density_g_per_ml' => $request->density_g_per_ml,
        ]);

        // Create ingredient's nutrients
        foreach ($request->ingredient_nutrients as $in) {
            IngredientNutrient::create([
                'ingredient_id' => $ingredient->id,
                'nutrient_id' => $in['nutrient_id'],
                'amount_per_100g' => $in['amount_per_100g'],
            ]);
        }

        return Redirect::route('ingredients.index')->with('message', 'Success! Ingredient created successfully.');
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
        $ingredient->load([
            'ingredient_nutrients:id,ingredient_id,nutrient_id,amount_per_100g',
            'ingredient_nutrients.nutrient:id,display_name,unit_id',
            'ingredient_nutrients.nutrient.unit:id,name'
        ]);

        return Inertia::render('Ingredients/Edit', [
            'ingredient' => $ingredient->only(['id', 'name', 'ingredient_category_id', 'density_g_per_ml', 'ingredient_nutrients']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ingredient $ingredient)
    {
        // Validate request
        $num_nutrients = Nutrient::count();
        $request->validate([
            'name' => ['required', 'min:1', 'max:500'],
            'category_id' => ['nullable', 'integer', 'in:ingredient_categories,id'],
            'density_g_per_ml' => ['nullable', 'numeric', 'gt:0'],
            'ingredient_nutrients' => ['required', 'array', 'max:' . $num_nutrients],
            'ingredient_nutrients.*.id' => ['required', 'distinct', 'integer', 'in:ingredient_nutrients,id'],
            'ingredient_nutrients.*.nutrient_id' => ['required', 'distinct', 'integer', 'in:nutrients,id'],
            'ingredient_nutrients.*.amount_per_100g' => ['required', 'numeric', 'gt:0'],
        ]);

        // Update ingredient
        $ingredient->update([
            'name' => $request->name,
            'fdc_id' => $request->fdc_id,
            'ingredient_category_id' => $request->ingredient_category_id,
            'density_g_per_ml' => $request->density_g_per_ml,
        ]);

        // Update ingredient's nutrients
        foreach ($request->ingredient_nutrients as $ing) {
            $dbIng = IngredientNutrient::find($ing[$id]);
            $dbIng->update([
                'ingredient_id' => $ingredient->id,
                'nutrient_id' => $ing['nutrient_id'],
                'amount_per_100g' => $ing['amount_per_100g'],
            ]);
        }

        return Redirect::route('ingredients.index')->with('message', 'Success! Ingredient updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ingredient $ingredient)
    {
        //
    }
}
