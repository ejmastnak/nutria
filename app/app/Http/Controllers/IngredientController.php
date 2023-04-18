<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\IngredientNutrient;
use App\Models\IngredientCategory;
use App\Models\Nutrient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class IngredientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        return Inertia::render('Ingredients/Index', [
            // 'user_ingredients' => Auth::user() ? Ingredient::where('user_id', Auth::user()->id)
            //     ->with('ingredient_category:id,name')
            //     ->get(['id', 'name', 'ingredient_category_id']) : [],
            'ingredients' => Ingredient::where('user_id', null)
                ->with('ingredient_category:id,name')
                ->get(['id', 'name', 'ingredient_category_id']),
            'user_ingredients' => Ingredient::where('ingredient_category_id', '11')
                ->where('user_id', null)
                ->skip(23)
                ->limit(5)
                ->with('ingredient_category:id,name')
                ->get(['id', 'name', 'ingredient_category_id']),
            'ingredient_categories' => IngredientCategory::all(['id', 'name']),
            "can_create" => $user ? ($user->can('create', Ingredient::class)) : false,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Ingredient::class);
        $nutrients = Nutrient::get(['id', 'display_name', 'unit_id']);
        $nutrients->load('unit:id,name');
        return Inertia::render('Ingredients/Create', [
            'ingredient_nutrients' => $nutrients->map(fn($nutrient) => [
                'id' => 0,
                'nutrient_id' => $nutrient->id,
                'amount_per_100g' => 0.0,
                'nutrient' => $nutrient
            ])
        ]);
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
        $this->authorize('view', $ingredient);
        $user = Auth::user();
        $ingredient->load('ingredient_category:id,name');
        return Inertia::render('Ingredients/Show', [
            'ingredient' => $ingredient->only([
                'id',
                'name',
                'ingredient_category_id',
                'ingredient_category',
                'density_g_per_ml'
            ]),
            'nutrient_profile' => NutrientProfileController::profileIngredient($ingredient->id),
            "can_edit" => $user ? ($user->can('update', $ingredient)) : false,
            "can_delete" => $user ? ($user->can('delete', $ingredient)) : false,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ingredient $ingredient)
    {
        $this->authorize('update', $ingredient);
        $user = Auth::user();
        $ingredient->load([
            'ingredient_nutrients:id,ingredient_id,nutrient_id,amount_per_100g',
            'ingredient_nutrients.nutrient:id,display_name,unit_id',
            'ingredient_nutrients.nutrient.unit:id,name'
        ]);
        return Inertia::render('Ingredients/Edit', [
            'ingredient' => $ingredient->only(['id', 'name', 'ingredient_category_id', 'density_g_per_ml', 'ingredient_nutrients']),
            "can_delete" => $user ? ($user->can('delete', $ingredient)) : false,
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
