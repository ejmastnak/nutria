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
use App\Services\NutrientProfileService;
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
        $user = Auth::user();
        $userId = $user ? $user->id : null;

        return Inertia::render('FoodLists/Index', [
            'food_lists' => FoodList::getForUser($userId),
            'can_create' => $user ? $user->can('create', FoodList::class) : false,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $userId = $user ? $user->id : null;

        return Inertia::render('FoodLists/Create', [
            'cloned_from_food_list' => null,
            'user_ingredients' => Ingredient::getForUserWithCategoryAndUnits($userId),
            'meals' => Meal::getForUserWithUnit($userId),
            'ingredient_categories' => IngredientCategory::getWithNameSorted(),
            'units' => Unit::getMassAndVolume(),
            'clone' => false,
            'can_create' => $user ? $user->can('create', FoodList::class) : false,
        ]);
    }

    /**
     * Like create, but form prefilled with an existing resource's values
     */
    public function clone(FoodList $foodList)
    {
        $user = Auth::user();
        $userId = $user ? $user->id : null;

        return Inertia::render('FoodLists/Create', [
            'food_list' => $foodList->withIngredientsAndMeals(),
            'user_ingredients' => Ingredient::getForUserWithCategoryAndUnits($userId),
            'meals' => Meal::getForUserWithUnit($userId),
            'ingredient_categories' => IngredientCategory::getWithNameSorted(),
            'units' => Unit::getMassAndVolume(),
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
    public function show(FoodList $foodList, NutrientProfileService $nutrientProfileService)
    {
        $user = Auth::user();
        $userId = $user ? $user->id : null;

        return Inertia::render('FoodLists/Show', [
            'food_list' => $foodList->withIngredientsAndMeals(),
            'nutrient_profiles' => $nutrientProfileService->getNutrientProfilesOfFoodList($foodList->id, $userId),
            'intake_guidelines' => IntakeGuideline::getForUser($userId),
            'nutrient_categories' => NutrientCategory::getWithName(),
            'units' => Unit::getMass(),
            'food_lists' => FoodList::getForUser($userId),
            'can_view' => $user ? $user->can('view', $foodList) : false,
            'can_create' => $user ? $user->can('create', FoodList::class) : false,
            'can_clone' => $user ? $user->can('clone', $foodList) : false,
            'can_update' => $user ? $user->can('update', $foodList) : false,
            'can_delete' => $user ? $user->can('delete', $foodList) : false,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FoodList $foodList)
    {
        $user = Auth::user();
        $userId = $user ? $user->id : null;

        return Inertia::render('FoodLists/Edit', [
            'food_list' => $foodList->withIngredientsAndMeals(),
            'user_ingredients' => Ingredient::getForUserWithCategoryAndUnits($userId),
            'meals' => Meal::getForUserWithUnit($userId),
            'ingredient_categories' => IngredientCategory::getWithNameSorted(),
            'units' => Unit::getMassAndVolume(),
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
    public function destroy(FoodList $foodList, FoodListService $foodListService)
    {
        $result = $foodListService->deleteFoodList($foodList);
        if ($result['success']) return Redirect::route('food-lists.index')->with('message', $result['message']);
        else return Redirect::route('food-lists.index')->with('message', $result['message']);
    }
}
