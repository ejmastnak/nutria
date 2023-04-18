<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use App\Models\Ingredient;
use App\Models\MealIngredient;
use App\Models\IngredientCategory;
use App\Models\Unit;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class MealController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Meals/Index', [
            'meals' => Auth::user() ? Meal::where('user_id', Auth::user()->id)->get(['id', 'name']) : [],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Meal::class);
        return Inertia::render('Meals/Create', [
            'ingredients' => Ingredient::where('user_id', null)
                ->with('ingredient_category:id')
                ->get(['id', 'name', 'ingredient_category_id']),
            'user_ingredients' => Auth::user() ? Ingredient::where('user_id', Auth::user()->id)
                ->with('ingredient_category:id')
                ->get(['id', 'name', 'ingredient_category_id']) : [],
            'ingredient_categories' => IngredientCategory::all(['id', 'name']),
            'units' => Unit::all(['id', 'name', 'is_mass', 'is_volume'])
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate request
        $request->validate([
            'name' => ['required', 'min:1', 'max:500'],
            'meal_ingredients' => ['required', 'array', 'min:1', 'max:500'],
            'meal_ingredients.*.ingredient_id' => ['required', 'integer', 'in:ingredients,id'],
            'meal_ingredients.*.amount' => ['required', 'numeric', 'gt:0'],
            'meal_ingredients.*.unit_id' => ['required', 'integer', 'in:units,id'],
        ]);

        // Create meal
        $meal_mass_in_grams = 0;
        $meal = Meal::create([
            'name' => $request->name,
            'mass_in_grams' => $meal_mass_in_grams
        ]);

        // Create meal's MealIngredients
        foreach ($request->meal_ingredients as $mi_data) {
            $mi = MealIngredient::create([
                'meal_id' => $meal->id,
                'ingredient_id' => $mi_data['ingredient_id'],
                'amount' => $mi_data['amount'],
                'unit_id' => $mi_data['unit_id'],
                'mass_in_grams' => UnitConversionController::to_grams_for_ingredient($mi_data['amount'], $mi_data['unit_id'], $mi_data['ingredient_id'])
            ]);
            $meal_mass_in_grams += $mi->mass_in_grams;
        }
        $meal->update([
          'mass_in_grams' => $meal_mass_in_grams
        ]);

        return Redirect::route('meals.index')->with('message', 'Success! Meal created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Meal $meal)
    {
        $this->authorize('view', $meal);
        $user = Auth::user();
        $meal->load([
            'meal_ingredients:id,meal_id,ingredient_id,amount,unit_id',
            'meal_ingredients.ingredient:id,name',
            'meal_ingredients.unit:id,name',
        ]);
        return Inertia::render('Meals/Show', [
            'meal' => $meal->only([
                'id',
                'name',
                'meal_ingredients'
            ]),
            'nutrient_profile' => NutrientProfileController::profileMeal($meal->id),
            "can_edit" => $user ? ($user->can('update', $meal)) : false,
            "can_delete" => $user ? ($user->can('delete', $meal)) : false,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Meal $meal)
    {
        $this->authorize('update', $meal);
        $user = Auth::user();
        $meal->load([
            'meal_ingredients:meal_id,ingredient_id,amount,unit_id',
            'meal_ingredients.ingredient:id,name',
            'meal_ingredients.unit:id,name',
        ]);
        return Inertia::render('Meals/Edit', [
            'meal' => $meal,
            'ingredients' => Ingredient::where('user_id', null)
                ->with('ingredient_category:id')
                ->get(['id', 'name', 'ingredient_category_id']),
            'user_ingredients' => Auth::user() ? Ingredient::where('user_id', Auth::user()->id)
                ->with('ingredient_category:id')
                ->get(['id', 'name', 'ingredient_category_id']) : [],
            'ingredient_categories' => IngredientCategory::all(['id', 'name']),
            'units' => Unit::all(['id', 'name', 'is_mass', 'is_volume']),
            "can_delete" => $user ? ($user->can('delete', $meal)) : false
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Meal $meal)
    {
        // Validate request
        $request->validate([
            'name' => ['required', 'min:1', 'max:500'],
            'meal_ingredients' => ['required', 'array', 'min:1', 'max:500'],
            'meal_ingredients.*.id' => ['required', 'integer', 'in:meal_ingredients,id'],
            'meal_ingredients.*.ingredient_id' => ['required', 'integer', 'in:ingredients,id'],
            'meal_ingredients.*.amount' => ['required', 'numeric', 'gt:0'],
            'meal_ingredients.*.unit_id' => ['required', 'integer', 'in:units,id'],
        ]);

        // Keep a running sum of constituent MealIngredient mass
        $meal_mass_in_grams = 0;

        // Find ID of all MealIngredients already associated with this meal in DB
        $dbMIs = $meal->meal_ingredients;
        $dbIDs = array_map(function ($dbMI) { return $dbMI['id']; }, $dbMIs->toArray());
        // Find ID of all MealIngredients associated with this meal in incoming request
        $requestMIs = $request->meal_ingredients;
        $requestIDs = array_map(function ($requestMI) { return $requestMI['id']; }, $requestMIs);

        // Delete all MealIngredients currently in DB but not in incoming request
        foreach ($dbMIs as $dbMI) {
            if (!in_array($dbMI['id'], $requestIDs)) {
                $dbMI->delete();
            }
        }

        // Create a new MealIngredient for any MealIngredient in request but not in DB
        foreach ($requestMIs as $requestMI) {
            if (!in_array($requestMI['id'], $dbIDs)) {
                $mi = MealIngredient::create([
                    'meal_id' => $meal->id,
                    'ingredient_id' => $requestMI['ingredient_id'],
                    'amount' => $requestMI['amount'],
                    'unit_id' => $requestMI['unit_id'],
                    'mass_in_grams' => UnitConversionController::to_grams_for_ingredient($requestMI['amount'], $requestMI['unit_id'], $requestMI['ingredient_id'])
                ]);
                $meal_mass_in_grams += $mi->mass_in_grams;
            }
        }

        // Update any MealIngredient that appear in both DB and incoming
        // request to reflect the state in request.
        foreach ($dbMIs as $dbMI) {
            // Is this dbMI also in requestMIs?
            $key = array_search($dbMI['id'], $requestIDs);
            if ($key !== false) {  // if a match was found
                $dbMI->update([
                    'meal_id' => $meal->id,
                    'ingredient_id' => $requestMIs[$key]['ingredient_id'],
                    'amount' => $requestMIs[$key]['amount'],
                    'unit_id' => $requestMIs[$key]['unit_id'],
                    'mass_in_grams' => UnitConversionController::to_grams_for_ingredient($requestMIs[$key]['amount'], $requestMIs[$key]['unit_id'], $requestMIs[$key]['ingredient_id'])
                ]);
                $meal_mass_in_grams += $dbMI->mass_in_grams;
            }
        }

        // Update meal
        $meal->update([
            'name' => $request->name,
            'mass_in_grams' => $meal_mass_in_grams,
        ]);

        return Redirect::route('meals.index')->with('message', 'Success! Meal updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Meal $meal)
    {
        //
    }

    public function validateStoreOrUpdateRequest(Request $request) {
        $request->validate([
            'name' => ['required', 'min:1', 'max:500'],
            'ingredients' => ['required', 'array', 'min:1', 'max:500'],
            'ingredients.*.ingredient_id' => ['required', 'integer', 'in:ingredients,id'],
            'ingredients.*.amount' => ['required', 'numeric', 'gt:0'],
            'ingredients.*.unit_id' => ['required', 'integer', 'in:units,id'],
        ]);
    }

}
