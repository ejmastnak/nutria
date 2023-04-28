<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use App\Models\Ingredient;
use App\Models\MealIngredient;
use App\Models\IngredientCategory;
use App\Models\NutrientCategory;
use App\Models\Unit;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

class MealController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        return Inertia::render('Meals/Index', [
            'meals' => Auth::user() ? Meal::where('user_id', Auth::user()->id)->get(['id', 'name']) : [],
            'can_create' => $user ? ($user->can('create', Meal::class)) : false,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Meal::class);
        $user = Auth::user();
        return Inertia::render('Meals/Create', [
            'meal' => null,
            'ingredients' => Ingredient::where('user_id', null)
                ->orWhere('user_id', $user ? $user->id : 0)
                ->get(['id', 'name', 'ingredient_category_id', 'density_g_per_ml']),
            'ingredient_categories' => IngredientCategory::all(['id', 'name']),
            'units' => Unit::all(['id', 'name', 'is_mass', 'is_volume']),
            'can_create' => $user ? ($user->can('create', Meal::class)) : false,
            'clone' => false
        ]);
    }

    /**
     * Like create, but form prefilled with an existing resource's values
     */
    public function clone(Meal $meal)
    {
        $this->authorize('clone', $meal);
        $user = Auth::user();

        $meal->load([
            'meal_ingredients:id,meal_id,ingredient_id,amount,unit_id',
            'meal_ingredients.ingredient:id,name',
            'meal_ingredients.unit:id,name',
        ]);

        return Inertia::render('Meals/Create', [
            'meal' => $meal->only([
                'id',
                'name',
                'meal_ingredients'
            ]),
            'ingredients' => Ingredient::where('user_id', null)
                ->orWhere('user_id', $user ? $user->id : 0)
                ->get(['id', 'name', 'ingredient_category_id', 'density_g_per_ml']),
            'ingredient_categories' => IngredientCategory::all(['id', 'name']),
            'units' => Unit::all(['id', 'name', 'is_mass', 'is_volume']),
            'can_create' => $user ? ($user->can('create', Meal::class)) : false,
            'can_delete' => $user ? ($user->can('delete', $meal)) : false,
            'clone' => true
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Meal::class);

        // Validate request
        $request->validate([
            'name' => ['required', 'min:1', 'max:500'],
            'meal_ingredients' => ['required', 'array', 'min:1', 'max:500'],
            'meal_ingredients.*.ingredient_id' => ['required', 'integer', 'exists:ingredients,id'],
            'meal_ingredients.*.amount' => ['required', 'numeric', 'gt:0'],
            'meal_ingredients.*.unit_id' => ['required', 'integer', 'exists:units,id'],
        ]);

        // Create meal
        $meal_mass_in_grams = 0;
        $meal = Meal::create([
            'name' => $request->name,
            'mass_in_grams' => $meal_mass_in_grams,
            'user_id' => $request->user()->id
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
                'mass_in_grams',
                'meal_ingredients'
            ]),
            'nutrient_profile' => NutrientProfileController::profileMeal($meal->id),
            'meals' => Meal::where('user_id', $user ? $user->id : 0)->get(['id', 'name']),
            'nutrient_categories' => NutrientCategory::all(['id', 'name']),
            'can_create' => $user ? ($user->can('create', Meal::class)) : false,
            'can_edit' => $user ? ($user->can('update', $meal)) : false,
            'can_delete' => $user ? ($user->can('delete', $meal)) : false,
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
            'meal_ingredients:id,meal_id,ingredient_id,amount,unit_id',
            'meal_ingredients.ingredient:id,name',
            'meal_ingredients.unit:id,name',
        ]);

        return Inertia::render('Meals/Edit', [
            'meal' => $meal->only([
                'id',
                'name',
                'meal_ingredients'
            ]),
            'ingredients' => Ingredient::where('user_id', null)
                ->orWhere('user_id', $user ? $user->id : 0)
                ->get(['id', 'name', 'ingredient_category_id', 'density_g_per_ml']),
            'ingredient_categories' => IngredientCategory::all(['id', 'name']),
            'units' => Unit::all(['id', 'name', 'is_mass', 'is_volume']),
            'can_create' => $user ? ($user->can('create', Meal::class)) : false,
            'can_delete' => $user ? ($user->can('delete', $meal)) : false
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Meal $meal)
    {
        $this->authorize('update', $meal);

        // Validate request
        $request->validate([
            'name' => ['required', 'min:1', 'max:500'],
            'meal_ingredients' => ['required', 'array', 'min:1', 'max:500'],
            'meal_ingredients.*.id' => ['required', 'integer'],
            'meal_ingredients.*.ingredient_id' => ['required', 'integer', 'exists:ingredients,id'],
            'meal_ingredients.*.amount' => ['required', 'numeric', 'gt:0'],
            'meal_ingredients.*.unit_id' => ['required', 'integer', 'exists:units,id'],
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

        return Redirect::route('meals.show', $meal->id)->with('message', 'Success! Meal updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Meal $meal)
    {
        if ($meal) {
            $meal->delete();
            return Redirect::route('meals.index')->with('message', 'Success! Meal deleted successfully.');
        }
        return Redirect::route('meals.index')->with('message', 'Failed to delete meal.');
    }

    public function validateStoreOrUpdateRequest(Request $request) {
        $request->validate([
            'name' => ['required', 'min:1', 'max:500'],
            'ingredients' => ['required', 'array', 'min:1', 'max:500'],
            'ingredients.*.ingredient_id' => ['required', 'integer', 'exists:ingredients,id'],
            'ingredients.*.amount' => ['required', 'numeric', 'gt:0'],
            'ingredients.*.unit_id' => ['required', 'integer', 'in:units,id'],
        ]);
    }

}
