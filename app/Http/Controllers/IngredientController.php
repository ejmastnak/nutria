<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\IngredientCategory;
use App\Models\NutrientCategory;
use App\Models\IntakeGuideline;
use App\Models\Nutrient;
use App\Models\Unit;
use App\Services\IngredientService;
use App\Services\NutrientProfileService;
use App\Http\Requests\IngredientStoreRequest;
use App\Http\Requests\IngredientUpdateRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class IngredientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $userId = $user ? $user->id : null;

        return Inertia::render('Ingredients/Index', [
            'user_ingredients' => Ingredient::getForUserWithCategoryAndUnits($userId),
            'ingredient_categories' => IngredientCategory::getWithNameSorted(),
            'can_create' => $user ? $user->can('create', Ingredient::class) : false,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();

        return Inertia::render('Ingredients/Create', [
            'ingredient' => null,
            'ingredient_categories' => IngredientCategory::getWithNameSorted(),
            'nutrients' => Nutrient::getWithUnit(),
            'nutrient_categories' => NutrientCategory::getWithName(),
            'units' => Unit::getMassAndVolume(),
            'can_create' => $user ? $user->can('create', Ingredient::class) : false
        ]);
    }

    /**
     * Like create, but form prefilled with an existing resource's values
     */
    public function clone(Ingredient $ingredient)
    {
        $user = Auth::user();

        return Inertia::render('Ingredients/Create', [
            'ingredient' => $ingredient->withCategoryUnitsNutrientsAndMeal(),
            'ingredient_categories' => IngredientCategory::getWithNameSorted(),
            'nutrients' => null,
            'nutrient_categories' => NutrientCategory::getWithName(),
            'units' => Unit::getMassAndVolume(),
            'can_create' => $user ? $user->can('create', Ingredient::class) : false
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(IngredientStoreRequest $request, IngredientService $ingredientService)
    {
        $ingredient = $ingredientService->storeIngredient($request->validated(), $request->user()->id);
        return Redirect::route('ingredients.show', $ingredient->id)->with('message', 'Success! Ingredient created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ingredient $ingredient, NutrientProfileService $nutrientProfileService)
    {
        $user = Auth::user();
        $userId = $user ? $user->id : null;

        return Inertia::render('Ingredients/Show', [
            'ingredient' => $ingredient->withCategoryUnitsAndMeal(),
            'nutrient_profiles' => $nutrientProfileService->getNutrientProfilesOfIngredient($ingredient->id, $userId),
            'intake_guidelines' => IntakeGuideline::getForUser($userId),
            'nutrient_categories' => NutrientCategory::getWithName(),
            'units' => Unit::getMassAndVolume(),
            'can_edit' => $user ? $user->can('update', $ingredient) : false,
            'can_clone' => $user ? $user->can('clone', $ingredient) : false,
            'can_delete' => $user ? $user->can('delete', $ingredient) : false,
            'can_create' => $user ? $user->can('create', Ingredient::class) : false,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ingredient $ingredient)
    {
        $user = Auth::user();

        return Inertia::render('Ingredients/Edit', [
            'ingredient' => $ingredient->withCategoryUnitsNutrientsAndMeal(),
            'ingredient_categories' => IngredientCategory::getWithNameSorted(),
            'nutrient_categories' => NutrientCategory::getWithName(),
            'units' => Unit::getMassAndVolume(),
            'can_clone' => $user ? $user->can('clone', $ingredient) : false,
            'can_delete' => $user ? $user->can('delete', $ingredient) : false,
            'can_create' => $user ? $user->can('create', Ingredient::class) : false,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(IngredientUpdateRequest $request, Ingredient $ingredient, IngredientService $ingredientService)
    {
        // Restrict updating ingredient derived from a meal
        if (!is_null($ingredient->meal_id)) return back()->with('message', 'Error. Updating this ingredient is intentionally restricted doing so would break the relationship with the parent meal. Update the parent meal instead.');
        $ingredientService->updateIngredient($request->validated(), $ingredient);
        return Redirect::route('ingredients.show', $ingredient->id)->with('message', 'Success! Ingredient updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ingredient $ingredient, IngredientService $ingredientService)
    {
        $result = $ingredientService->deleteIngredient($ingredient);
        if ($result['success']) return Redirect::route('ingredients.index')->with('message', $result['message']);
        else if ($result['restricted']) return back()->with('message', $result['message']);
        else return Redirect::route('ingredients.index')->with('message', $result['message']);
    }

}
