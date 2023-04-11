<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use App\Models\MealIngredient;
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
        // Validate request
        $request->validate([
            'name' => ['required', 'min:1', 'max:500'],
            'meal_ingredients' => ['required', 'array', 'min:1', 'max:500'],
            'meal_ingredients.*.ingredient_id' => ['required', 'integer', 'in:ingredients,id'],
            'meal_ingredients.*.amount' => ['required', 'numeric', 'gt:0'],
            'meal_ingredients.*.unit_id' => ['required', 'integer', 'in:units,id'],
        ]);

        // Create meal
        $meal = Meal::create([
            'name' => $request->name,
        ]);

        // Create meal's MealIngredients
        foreach ($request->meal_ingredients as $mi) {
            MealIngredient::create([
                'meal_id' => $meal->id,
                'ingredient_id' => $mi['ingredient_id'],
                'amount' => $mi['amount'],
                'unit_id' => $mi['unit_id''],
                'mass_in_grams' => UnitConversionController::to_grams_for_ingredient($mi['amount'], $mi['unit_id'], $mi['ingredient_id'])
            ]);
        }

        return Redirect::route('meals.index')->with('message', 'Success! Meal created successfully.');
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
        // Validate request
        $request->validate([
            'name' => ['required', 'min:1', 'max:500'],
            'meal_ingredients' => ['required', 'array', 'min:1', 'max:500'],
            'meal_ingredients.*.id' => ['required', 'integer', 'in:meal_ingredients,id'],
            'meal_ingredients.*.ingredient_id' => ['required', 'integer', 'in:ingredients,id'],
            'meal_ingredients.*.amount' => ['required', 'numeric', 'gt:0'],
            'meal_ingredients.*.unit_id' => ['required', 'integer', 'in:units,id'],
        ]);

        // Update meal
        $meal->update([
            'name' => $request->name,
        ]);

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
                MealIngredient::create([
                    'meal_id' => $meal->id,
                    'ingredient_id' => $requestMI['ingredient_id'],
                    'amount' => $requestMI['amount'],
                    'unit_id' => $requestMI['unit_id''],
                    'mass_in_grams' => UnitConversionController::to_grams_for_ingredient($requestMI['amount'], $requestMI['unit_id'], $requestMI['ingredient_id'])
                ]);
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
                    'unit_id' => $requestMIs[$key]['unit_id''],
                    'mass_in_grams' => UnitConversionController::to_grams_for_ingredient($requestMIs[$key]['amount'], $requestMIs[$key]['unit_id'], $requestMIs[$key]['ingredient_id'])
                ]);
            }
        }

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
