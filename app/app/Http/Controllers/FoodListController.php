<?php

namespace App\Http\Controllers;

use App\Models\FoodList;
use App\Models\FoodListIngredient;
use App\Models\FoodListMeal;
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
        // Validate request
        $request->validate([
            'name' => ['required', 'min:1', 'max:500'],
            'food_list_ingredients' => [
                'required',
                'array',
                function ($attribute, $value, $fail) use($request) {
                    // food_list_ingredients must contain at least one element
                    // if food_list_meals is empty
                    if (count($request->food_list_ingredients) == 0 && count($request->food_list_meals) == 0) {
                        $fail('Include at least one ingredient or meal.');
                    }
                },
                'max:100'
            ],
            'food_list_ingredients.*.ingredient_id' => ['required', 'integer', 'in:ingredients,id'],
            'food_list_ingredients.*.amount' => ['required', 'numeric', 'gt:0'],
            'food_list_ingredients.*.unit_id' => ['required', 'integer', 'in:units,id'],
            'food_list_meals' => [
                'required',
                'array',
                function ($attribute, $value, $fail) use($request) {
                    // food_list_meals must contain at least one element if
                    // food_list_ingredients is empty
                    if (count($request->food_list_meals) == 0 && count($request->food_list_ingredients) == 0) {
                        $fail('Include at least one meal or ingredient.');
                    }
                },
                'max:100'
            ],
            'food_list_meals.*.meal_id' => ['required', 'integer', 'in:meals,id'],
            'food_list_meals.*.amount' => ['required', 'numeric', 'gt:0'],
            'food_list_meals.*.unit_id' => ['required', 'integer', 'in:units,id'],
        ]);

        // Create food list
        $food_list_mass_in_grams = 0;
        $food_list = FoodList::create([
            'name' => $request->name,
            'mass_in_grams' => $food_list_mass_in_grams
        ]);

        // Create FoodListIngredients
        foreach ($request->food_list_ingredients as $fli) {
            FoodListIngredient::create([
                'food_list_id' => $food_list->id,
                'ingredient_id' => $fli['ingredient_id'],
                'amount' => $fli['amount'],
                'unit_id' => $fli['unit_id'],
                'mass_in_grams' => UnitConversionController::to_grams_for_ingredient($fli['amount'], $fli['unit_id'], $fli['ingredient_id'])
            ]);
            $food_list_mass_in_grams += $fli->mass_in_grams;
        }

        // Create FoodListMeals
        foreach ($request->food_list_meals as $flm) {
            FoodListMeal::create([
                'food_list_id' => $food_list->id,
                'meal_id' => $flm['meal_id'],
                'amount' => $flm['amount'],
                'unit_id' => $flm['unit_id'],
                'mass_in_grams' => UnitConversionController::mass_to_grams($flm['amount'], $flm['unit_id'])
            ]);
            $food_list_mass_in_grams += $flm->mass_in_grams;
        }
        $food_list->update([
          'mass_in_grams' => $food_list_mass_in_grams
        ]);

        return Redirect::route('food-lists.index')->with('message', 'Success! Food list created successfully.');
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
            'foodLists' => FoodList::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FoodList $foodList)
    {
        // Validate request
        $request->validate([
            'name' => ['required', 'min:1', 'max:500'],
            'food_list_ingredients' => [
                'required',
                'array',
                function ($attribute, $value, $fail) use($request) {
                    // food_list_ingredients must contain at least one element
                    // if food_list_meals is empty
                    if (count($request->food_list_ingredients) == 0 && count($request->food_list_meals) == 0) {
                        $fail('Include at least one ingredient or meal.');
                    }
                },
                'max:100'
            ],
            'food_list_ingredients.*.id' => ['required', 'integer', 'in:food_list_ingredients,id'],
            'food_list_ingredients.*.ingredient_id' => ['required', 'integer', 'in:ingredients,id'],
            'food_list_ingredients.*.amount' => ['required', 'numeric', 'gt:0'],
            'food_list_ingredients.*.unit_id' => ['required', 'integer', 'in:units,id'],
            'food_list_meals' => [
                'required',
                'array',
                function ($attribute, $value, $fail) use($request) {
                    // food_list_meals must contain at least one element if
                    // food_list_ingredients is empty
                    if (count($request->food_list_meals) == 0 && count($request->food_list_ingredients) == 0) {
                        $fail('Include at least one meal or ingredient.');
                    }
                },
                'max:100'
            ],
            'food_list_meals.*.id' => ['required', 'integer', 'in:food_list_meals,id'],
            'food_list_meals.*.meal_id' => ['required', 'integer', 'in:meals,id'],
            'food_list_meals.*.amount' => ['required', 'numeric', 'gt:0'],
            'food_list_meals.*.unit_id' => ['required', 'integer', 'in:units,id'],
        ]);

        // Keep a running sum of constituent ingredient and meal masses
        $food_list_mass_in_grams = 0;

        // ------------------------------------------------------------------ //
        // Update FoodListIngredients
        // ------------------------------------------------------------------ //
        // Find ID of all FoodListIngredients already associated with this food list in DB
        $dbFLIs = $foodList->food_list_ingredients;
        $dbFLIIDS = array_map(function ($dbFLI) { return $dbFLI['id']; }, $dbFLIs->toArray());
        // Find ID of all FoodListIngredients associated with this food list in incoming request
        $requestFLIs = $request->food_list_ingredients;
        $requestFLIIDs = array_map(function ($requestFLI) { return $requestFLI['id']; }, $requestFLIs);

        // Delete all FoodListIngredients currently in DB but not in incoming request
        foreach ($dbFLIs as $dbFLI) {
            if (!in_array($dbFLI['id'], $requestFLIIDs)) {
                $dbFLI->delete();
            }
        }

        // Create a new FoodListIngredient for any FoodListIngredient in
        // request but not in DB
        foreach ($requestFLIs as $requestFLI) {
            if (!in_array($requestFLI['id'], $dbFLIIDS)) {
                $fli = FoodListIngredient::create([
                    'food_list_id' => $foodList->id,
                    'ingredient_id' => $requestFLI['ingredient_id'],
                    'amount' => $requestFLI['amount'],
                    'unit_id' => $requestFLI['unit_id'],
                    'mass_in_grams' => UnitConversionController::to_grams_for_ingredient($requestFLI['amount'], $requestFLI['unit_id'], $requestFLI['ingredient_id'])
                ]);
                $food_list_mass_in_grams += $fli->mass_in_grams;
            }
        }

        // Update any FoodListIngredient that appear in both DB and incoming
        // request to reflect the state in request.
        foreach ($dbFLIs as $dbFLI) {
            // Is this dbFLI also in requestFLIs?
            $key = array_search($dbFLI['id'], $requestFLIIDs);
            if ($key !== false) {  // if a match was found
                $dbFLI->update([
                    'food_list_id' => $foodList->id,
                    'ingredient_id' => $requestFLIs[$key]['ingredient_id'],
                    'amount' => $requestFLIs[$key]['amount'],
                    'unit_id' => $requestFLIs[$key]['unit_id'],
                    'mass_in_grams' => UnitConversionController::to_grams_for_ingredient($requestFLIs[$key]['amount'], $requestFLIs[$key]['unit_id'], $requestFLIs[$key]['ingredient_id'])
                ]);
                $food_list_mass_in_grams += $dbFLI->mass_in_grams;
            }
        }

        // ------------------------------------------------------------------ //
        // Update FoodListMeals
        // ------------------------------------------------------------------ //
        // Find ID of all FoodListMeals already associated with this food list in DB
        $dbFLMs = $foodList->food_list_meals;
        $dbFLMIDS = array_map(function ($dbFLM) { return $dbFLM['id']; }, $dbFLMs->toArray());
        // Find ID of all FoodListMeals associated with this food list in
        // incoming request
        $requestFLMs = $request->food_list_meals;
        $requestFLMIDs = array_map(function ($requestFLM) { return $requestFLM['id']; }, $requestFLMs);

        // Delete all FoodListMeals currently in DB but not in incoming request
        foreach ($dbFLMs as $dbFLM) {
            if (!in_array($dbFLM['id'], $requestFLMIDs)) {
                $dbFLM->delete();
            }
        }

        // Create a new FoodListMeal for any FoodListMeals in request but not
        // in DB
        foreach ($requestFLMs as $requestFLM) {
            if (!in_array($requestFLM['id'], $dbFLMIDS)) {
                $flm = FoodListMeal::create([
                    'food_list_id' => $foodList->id,
                    'meal_id' => $requestFLM['meal_id'],
                    'amount' => $requestFLM['amount'],
                    'unit_id' => $requestFLM['unit_id'],
                    'mass_in_grams' => UnitConversionController::mass_to_grams($requestFLM['amount'], $requestFLM['unit_id'])
                ]);
                $food_list_mass_in_grams += $flm->mass_in_grams;
            }
        }

        // Update any FoodListMeal that appears in both DB and incoming request
        // to reflect the state in request.
        foreach ($dbFLMs as $dbFLM) {
            // Is this dbFLM also in requestFLMs?
            $key = array_search($dbFLM['id'], $requestFLMIDs);
            if ($key !== false) {  // if a match was found
                $dbFLM->update([
                    'food_list_id' => $foodList->id,
                    'meal_id' => $requestFLMs[$key]['meal_id'],
                    'amount' => $requestFLMs[$key]['amount'],
                    'unit_id' => $requestFLMs[$key]['unit_id'],
                    'mass_in_grams' => UnitConversionController::mass_to_grams($requestFLMs[$key]['amount'], $requestFLMs[$key]['unit_id'])
                ]);
                $food_list_mass_in_grams += $dbFLM->mass_in_grams;
            }
        }

        // Update FoodList
        $foodList->update([
            'name' => $request->name,
            'mass_in_grams' => $food_list_mass_in_grams
        ]);

        return Redirect::route('food-lists.index')->with('message', 'Success! Food list updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FoodList $foodList)
    {
        //
    }
}
