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

    /**
     * Incoming request takes the form
     *
     * {
     *   "name": "Foo",
     *   "food_list_ingredients": [
     *     {
     *       "ingredient_id": 0,
     *       "amount": 0.0,
     *       "unit_id": 0
     *     }
     *   ],
     *   "food_list_meals": [
     *     {
     *       "meal_id": 0,
     *       "amount": 0.0,
     *       "unit_id": 0
     *     }
     *   ]
     * }
     *
     * Validation checks that:
     *
     * - name is a string with sane min and max length.
     * - food_list_ingredients is an array with at least one item if food_list_meals is empty (and e.g. fewer than 1000 items)
     * - food_list_ingredients.*.ingredient_id is a required integer present in ingredients,id
     * - food_list_ingredients.*.amount is a positive float
     * - food_list_ingredients.*.unit_id i a required integer present in units,id
     * - food_list_meals is an array with at least one item if food_list_ingredients is empty (and e.g. fewer than 1000 items)
     * - food_list_meals.*.meal_id is a required integer present in meals,id
     * - food_list_meals.*.amount is a positive float
     * - food_list_meals.*.unit_id i a required integer present in units,id

     *      
     */
    public function validateStoreOrUpdateRequest(Request $request) {
        $num_nutrients = Nutrient::count();
        $request->validate([
            'name' => ['required', 'min:1', 'max:500'],
            'food_list_ingredients' => [
                'required',
                'array',
                function ($attribute, $value, $fail) use($request) {
                    // food_list_ingredients must contain at least one element
                    // if food_list_meals is empty
                    if (count($request['food_list_ingredients']) == 0 && count($request['food_list_meals']) == 0) {
                        $fail('Include at least one ingredient.');
                    }
                },
                'max:500'
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
                    if (count($request['food_list_meals']) == 0 && count($request['food_list_ingredients']) == 0) {
                        $fail('Include at least one meal.');
                    }
                },
                'max:500'
            ],
            'food_list_meals.*.meal_id' => ['required', 'integer', 'in:meals,id'],
            'food_list_meals.*.amount' => ['required', 'numeric', 'gt:0'],
            'food_list_meals.*.unit_id' => ['required', 'integer', 'in:units,id'],
        ]);
    }

}
