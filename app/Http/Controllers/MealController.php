<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use App\Models\Ingredient;
use App\Models\IngredientCategory;
use App\Models\NutrientCategory;
use App\Models\IntakeGuideline;
use App\Models\Unit;
use App\Http\Requests\StoreMealRequest;
use App\Http\Requests\UpdateMealRequest;
use App\Services\MealService;
use App\Services\NutrientProfileService;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MealController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $userId = $user ? $user->id : null;
        return Inertia::render('Meals/Index', [
            'meals' => Meal::getForUserWithChildIngredient($userId),
            'can_create' => $user ? $user->can('create', Meal::class) : false,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $userId = $user ? $user->id : null;

        return Inertia::render('Meals/Create', [
            'meal' => null,
            'user_ingredients' => Ingredient::getForUserWithCategoryAndUnits($userId),
            'units' => Unit::getMassAndVolume(),
            'meals' => Meal::getForUser($userId),
            'can_create' => $user ? $user->can('create', Meal::class) : false
        ]);
    }

    /**
     * Like create, but form prefilled with an existing resource's values
     */
    public function clone(Meal $meal)
    {
        $user = Auth::user();
        $userId = $user ? $user->id : null;
        return Inertia::render('Meals/Create', [
            'meal' => $meal->withIngredientsAndChildIngredient(),
            'user_ingredients' => Ingredient::getForUserWithCategoryAndUnits($userId),
            'units' => Unit::getMassAndVolume(),
            'meals' => Meal::getForUser($userId),
            'can_view' => $user ? $user->can('view', $meal) : false,
            'can_create' => $user ? $user->can('create', Meal::class) : false,
            'can_clone' => $user ? $user->can('clone', $meal) : false,
            'can_update' => $user ? $user->can('update', $meal) : false,
            'can_delete' => $user ? $user->can('delete', $meal) : false,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMealRequest $request, MealService $mealService)
    {
        $meal = $mealService->storeMeal($request->validated(), $request->user()->id);
        return Redirect::route('meals.show', $meal->id)->with('message', 'Success! Meal created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Meal $meal, NutrientProfileService $nutrientProfileService)
    {
        $user = Auth::user();
        $userId = $user ? $user->id : null;

        return Inertia::render('Meals/Show', [
            'meal' => $meal->withIngredientsAndChildIngredient(),
            'nutrient_profiles' => $nutrientProfileService->getNutrientProfilesOfMeal($meal->id, $userId),
            'intake_guidelines' => IntakeGuideline::getForUser($userId),
            'nutrient_categories' => NutrientCategory::getWithName(),
            'units' => Unit::getMass(),
            'meals' => Meal::getForUser($userId),
            'can_view' => $user ? $user->can('view', $meal) : false,
            'can_create' => $user ? $user->can('create', Meal::class) : false,
            'can_clone' => $user ? $user->can('clone', $meal) : false,
            'can_update' => $user ? $user->can('update', $meal) : false,
            'can_delete' => $user ? $user->can('delete', $meal) : false,
            'can_save_as_ingredient' => $user ? $user->can('saveAsIngredient', $meal) : false,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Meal $meal)
    {
        $user = Auth::user();
        $userId = $user ? $user->id : null;

        return Inertia::render('Meals/Edit', [
            'meal' => $meal->withIngredientsAndChildIngredient(),
            'user_ingredients' => Ingredient::getForUserWithCategoryAndUnits($userId),
            'units' => Unit::getMassAndVolume(),
            'meals' => Meal::getForUser($userId),
            'can_view' => $user ? $user->can('view', $meal) : false,
            'can_create' => $user ? $user->can('create', Meal::class) : false,
            'can_clone' => $user ? $user->can('clone', $meal) : false,
            'can_update' => $user ? $user->can('update', $meal) : false,
            'can_delete' => $user ? $user->can('delete', $meal) : false,
            'can_create' => $user ? $user->can('create', Meal::class) : false,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMealRequest $request, Meal $meal, MealService $mealService, NutrientProfileService $nutrientProfileService)
    {
        $mealService->updateMeal($request->validated(), $meal, $nutrientProfileService);
        return Redirect::route('meals.show', $meal->id)->with('message', 'Success! Meal updated successfully.');
    }

    /**
     *  Saves a Meal as a single Ingredient.
     */
    public function saveAsIngredient(Meal $meal, MealService $mealService, NutrientProfileService $nutrientProfileService) {
        if (is_null($meal)) return back()->with('message', 'Error. Failed to create ingredient—the parent meal could not be resolved.');  // basic validation
        $user = Auth::user();
        $ingredient = $mealService->saveAsIngredient($meal, $user->id, $nutrientProfileService);
        return Redirect::route('ingredients.show', $ingredient->id)->with('message', 'Success! Ingredient successfully created.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Meal $meal, MealService $mealService)
    {
        $result = $mealService->deleteMeal($meal);
        if ($result['success']) return Redirect::route('meals.index')->with('message', $result['message']);
        else if ($result['restricted']) return back()->with('message', $result['message']);
        else return Redirect::route('meals.index')->with('message', $result['message']);
    }

}
