<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use App\Models\Nutrient;
use App\Models\Ingredient;
use App\Models\IngredientCategory;
use App\Models\IngredientNutrient;
use App\Models\NutrientCategory;
use App\Models\IntakeGuideline;
use App\Models\Unit;
use App\Http\Requests\MealStoreRequest;
use App\Http\Requests\MealUpdateRequest;
use App\Services\MealService;
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
        $this->authorize('viewAny', Meal::class);
        $user = Auth::user();

        return Inertia::render('Meals/Index', [
            'meals' => Auth::user() ? Meal::where('user_id', Auth::user()->id)->with('ingredient:id,meal_id,name')->get(['id', 'name']) : [],
            'can_create' => $user ? $user->can('create', Meal::class) : false,
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
            'meals' => Meal::where('user_id', $user ? $user->id : 0)->get(['id', 'name']),
            'ingredients' => Ingredient::where('user_id', null)
                ->orWhere('user_id', $user ? $user->id : 0)
                ->get(['id', 'name', 'ingredient_category_id', 'density_g_per_ml']),
            'ingredient_categories' => IngredientCategory::orderBy('name', 'asc')->get(['id', 'name']),
            'units' => Unit::all(['id', 'name', 'is_mass', 'is_volume']),
            'clone' => false,
            'can_view' => false,  // only relevant for clone
            'can_create' => $user ? $user->can('create', Meal::class) : false
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
            'meals' => Meal::where('user_id', $user ? $user->id : 0)->get(['id', 'name']),
            'ingredients' => Ingredient::where('user_id', null)
                ->orWhere('user_id', $user ? $user->id : 0)
                ->get(['id', 'name', 'ingredient_category_id', 'density_g_per_ml']),
            'ingredient_categories' => IngredientCategory::orderBy('name', 'asc')->get(['id', 'name']),
            'units' => Unit::all(['id', 'name', 'is_mass', 'is_volume']),
            'clone' => true,
            'can_view' => $user ? $user->can('view', $meal) : false,
            'can_create' => $user ? $user->can('create', Meal::class) : false,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MealStoreRequest $request, MealService $mealService)
    {
        $meal = $mealService->storeMeal($request->validated(), $request->user()->id);
        return Redirect::route('meals.show', $meal->id)->with('message', 'Success! Meal created successfully.');
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
            'ingredient:id,meal_id,name'
        ]);

        return Inertia::render('Meals/Show', [
            'meal' => $meal->only([
                'id',
                'name',
                'mass_in_grams',
                'ingredient',
                'meal_ingredients',
            ]),
            'nutrient_profiles' => NutrientProfileController::getNutrientProfilesOfMeal($meal->id),
            'intake_guidelines' => IntakeGuideline::where('user_id', null)
            ->orWhere('user_id', $user ? $user->id : 0)
            ->orderBy('id', 'asc')
            ->get(['id', 'name']),
            'meals' => Meal::where('user_id', $user ? $user->id : 0)->get(['id', 'name']),
            'nutrient_categories' => NutrientCategory::all(['id', 'name']),
            'can_edit' => $user ? $user->can('update', $meal) : false,
            'can_clone' => $user ? $user->can('clone', $meal) : false,
            'can_delete' => $user ? $user->can('delete', $meal) : false,
            'can_create' => $user ? $user->can('create', Meal::class) : false,
            'can_create_ingredient' => $user ? $user->can('create', Ingredient::class) : false,
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
            'ingredient:id,meal_id,name'
        ]);

        return Inertia::render('Meals/Edit', [
            'meal' => $meal->only([
                'id',
                'name',
                'ingredient',
                'meal_ingredients'
            ]),
            'meals' => Meal::where('user_id', $user ? $user->id : 0)->get(['id', 'name']),
            'ingredients' => Ingredient::where('user_id', null)
            ->orWhere('user_id', $user ? $user->id : 0)
            ->get(['id', 'name', 'ingredient_category_id', 'density_g_per_ml']),
            'ingredient_categories' => IngredientCategory::orderBy('name', 'asc')->get(['id', 'name']),
            'units' => Unit::all(['id', 'name', 'is_mass', 'is_volume']),
            'can_view' => $user ? $user->can('view', $meal) : false,
            'can_clone' => $user ? $user->can('clone', $meal) : false,
            'can_delete' => $user ? $user->can('delete', $meal) : false,
            'can_create' => $user ? $user->can('create', Meal::class) : false,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MealUpdateRequest $request, Meal $meal, MealService $mealService)
    {
        $mealService->updateMeal($request->validated(), $meal);
        return Redirect::route('meals.show', $meal->id)->with('message', 'Success! Meal updated successfully.');
    }

    /**
     *  Saves a Meal as a single Ingredient.
     */
    public function saveAsIngredient(Meal $meal, MealService $mealService) {
        if (is_null($meal)) return back()->with('message', 'Error. Failed to create ingredient—the parent meal could not be resolved.');  // basic validation
        $user = Auth::user();
        $ingredient = $mealService->saveAsIngredient($meal, $user->id);
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
