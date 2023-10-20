<?php

namespace App\Http\Controllers;

use App\Models\FoodList;
use App\Models\Ingredient;
use App\Models\Meal;
use App\Models\NutrientCategory;
use App\Models\IngredientCategory;
use App\Models\Unit;
use App\Models\IntakeGuideline;
use App\Http\Requests\FoodListStoreRequest;
use App\Http\Requests\FoodListUpdateRequest;
use App\Services\FoodListService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class FoodListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', FoodList::class);
        $user = Auth::user();

        return Inertia::render('FoodLists/Index', [
            'food_lists' => Auth::user() ? FoodList::where('user_id', Auth::user()->id)->get(['id', 'name', 'mass_in_grams']) : [],
            'can_create' => $user ? $user->can('create', FoodList::class) : false,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', FoodList::class);
        $user = Auth::user();

        return Inertia::render('FoodLists/Create', [
            'food_list' => null,
            'food_lists' => FoodList::where('user_id', $user ? $user->id : 0)->get(['id', 'name']),
            'ingredients' => Ingredient::where('user_id', null)
                ->orWhere('user_id', $user ? $user->id : 0)
                ->get(['id', 'name', 'ingredient_category_id', 'density_g_per_ml']),
            'meals' => Meal::where('user_id', $user ? $user->id : 0)
                ->get(['id', 'name', 'mass_in_grams']),
            'ingredient_categories' => IngredientCategory::orderBy('name', 'asc')->get(['id', 'name']),
            'units' => Unit::all(['id', 'name', 'is_mass', 'is_volume']),
            'clone' => false,
            'can_view' => false,  // only relevant for clone
            'can_create' => $user ? $user->can('create', FoodList::class) : false,
        ]);
    }

    /**
     * Like create, but form prefilled with an existing resource's values
     */
    public function clone(FoodList $foodList)
    {
        $this->authorize('clone', $foodList);
        $user = Auth::user();

        $foodList->load([
            'food_list_ingredients:id,ingredient_id,food_list_id,amount,unit_id',
            'food_list_ingredients.ingredient:id,name,density_g_per_ml',
            'food_list_ingredients.unit:id,name',
            'food_list_meals:id,meal_id,food_list_id,amount,unit_id',
            'food_list_meals.meal:id,name',
            'food_list_meals.unit:id,name'
        ]);

        return Inertia::render('FoodLists/Create', [
            'food_list' => $foodList->only(['id', 'name', 'food_list_ingredients', 'food_list_meals']),
            'food_lists' => FoodList::where('user_id', $user ? $user->id : 0)->get(['id', 'name']),
            'ingredients' => Ingredient::where('user_id', null)
                ->orWhere('user_id', $user ? $user->id : 0)
                ->get(['id', 'name', 'ingredient_category_id', 'density_g_per_ml']),
            'meals' => Meal::where('user_id', $user ? $user->id : 0)
                ->get(['id', 'name', 'mass_in_grams']),
            'ingredient_categories' => IngredientCategory::orderBy('name', 'asc')->get(['id', 'name']),
            'units' => Unit::all(['id', 'name', 'is_mass', 'is_volume']),
            'clone' => true,
            'can_view' => $user ? $user->can('view', $foodList) : false,
            'can_create' => $user ? $user->can('create', FoodList::class) : false,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FoodListStoreRequest $request, FoodListService $foodListService)
    {
        $foodList = $foodListService->storeFoodList($request->validated(), $request->user()->id);
        return Redirect::route('food-lists.show', $foodList->id)->with('message', 'Success! Food List created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FoodList $foodList)
    {
        $this->authorize('view', $foodList);
        $user = Auth::user();

        $foodList->load([
            'food_list_ingredients:id,ingredient_id,food_list_id,amount,unit_id',
            'food_list_ingredients.ingredient:id,name',
            'food_list_ingredients.unit:id,name',
            'food_list_meals:id,meal_id,food_list_id,amount,unit_id',
            'food_list_meals.meal:id,name',
            'food_list_meals.unit:id,name'
        ]);

        return Inertia::render('FoodLists/Show', [
            'food_list' => $foodList->only([
                'id',
                'name',
                'mass_in_grams',
                'food_list_ingredients',
                'food_list_meals'
            ]),
            'nutrient_profiles' => NutrientProfileController::getNutrientProfilesOfFoodList($foodList->id),
            'intake_guidelines' => IntakeGuideline::where('user_id', null)
            ->orWhere('user_id', $user ? $user->id : 0)
            ->orderBy('id', 'asc')
            ->get(['id', 'name']),
            'food_lists' => FoodList::where('user_id', $user ? $user->id : 0)->get(['id', 'name']),
            'nutrient_categories' => NutrientCategory::all(['id', 'name']),
            'can_edit' => $user ? $user->can('update', $foodList) : false,
            'can_clone' => $user ? $user->can('clone', $foodList) : false,
            'can_delete' => $user ? $user->can('delete', $foodList) : false,
            'can_create' => $user ? $user->can('create', FoodList::class) : false
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FoodList $foodList)
    {
        $this->authorize('update', $foodList);
        $user = Auth::user();

        $foodList->load([
            'food_list_ingredients:id,ingredient_id,food_list_id,amount,unit_id',
            'food_list_ingredients.ingredient:id,name,density_g_per_ml',
            'food_list_ingredients.unit:id,name',
            'food_list_meals:id,meal_id,food_list_id,amount,unit_id',
            'food_list_meals.meal:id,name',
            'food_list_meals.unit:id,name'
        ]);

        return Inertia::render('FoodLists/Edit', [
            'food_list' => $foodList->only(['id', 'name', 'food_list_ingredients', 'food_list_meals']),
            'food_lists' => FoodList::where('user_id', $user ? $user->id : 0)->get(['id', 'name']),
            'ingredients' => Ingredient::where('user_id', null)
                ->orWhere('user_id', $user ? $user->id : 0)
                ->get(['id', 'name', 'ingredient_category_id', 'density_g_per_ml']),
            'meals' => Meal::where('user_id', $user ? $user->id : 0)
                ->get(['id', 'name', 'mass_in_grams']),
            'ingredient_categories' => IngredientCategory::orderBy('name', 'asc')->get(['id', 'name']),
            'units' => Unit::all(['id', 'name', 'is_mass', 'is_volume']),
            'can_view' => $user ? $user->can('view', $foodList) : false,
            'can_clone' => $user ? $user->can('clone', $foodList) : false,
            'can_delete' => $user ? $user->can('delete', $foodList) : false,
            'can_create' => $user ? $user->can('create', FoodList::class) : false
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FoodListUpdateRequest $request, FoodList $foodList, FoodListService $foodListService)
    {
        $foodListService->updateFoodList($request->validated(), $foodList);
        return Redirect::route('food-lists.show', $foodList->id)->with('message', 'Success! Food List updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FoodList $foodList)
    {
        $this->authorize('delete', $foodList);

        if ($foodList) {
            $foodList->delete();
            return Redirect::route('food-lists.index')->with('message', 'Success! Food List deleted successfully.');
        }
        return Redirect::route('food-lists.index')->with('message', 'Failed to delete food list.');
    }
}
