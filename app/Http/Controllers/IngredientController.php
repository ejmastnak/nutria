<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\IngredientCategory;
use App\Models\NutrientCategory;
use App\Models\Nutrient;
use App\Models\IntakeGuideline;
use App\Services\IngredientService;
use App\Services\NutrientProfileService;
use App\Http\Requests\IngredientStoreRequest;
use App\Http\Requests\IngredientUpdateRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            'ingredients' => Ingredient::getForUserWithIngredientCategory($userId),
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
        $userId = $user ? $user->id : null;

        return Inertia::render('Ingredients/Create', [
            'cloned_from_ingredient' => null,
            'ingredient_categories' => IngredientCategory::getWithNameSorted(),
            'nutrient_categories' => NutrientCategory::getWithName(),
            'units' => Unit::getMassAndVolumeUnits(),
            'can_create' => $user ? $user->can('create', Ingredient::class) : false
        ]);
    }

    /**
     * Like create, but form prefilled with an existing resource's values
     */
    public function clone(Ingredient $ingredient)
    {
        $user = Auth::user();

        // The raw query is to solve the following problem: When cloning
        // ingredients for ingredients from FDA database which don't have all
        // nutrients present, you need to add the missing nutrients and set
        // their `amount_per_100_g` values to zero. I found this to be most
        // straightforward with a raw query and left join than using the ORM.
        $query = "
        select
          nutrients.id as nutrient_id,
          nutrients.display_name as display_name,
          coalesce(round(ingredient_nutrients.amount_per_100g, 2),0) as amount_per_100g,
          nutrients.nutrient_category_id as nutrient_category_id,
          units.id as unit_id,
          units.name as unit_name
        from nutrients
        left join ingredient_nutrients
          on ingredient_nutrients.nutrient_id
          = nutrients.id
          and ingredient_nutrients.ingredient_id
          = :ingredient_id
        inner join units
          on units.id
          = nutrients.unit_id
        order by nutrients.display_order_id;
        ";

        $rawIngredientNutrients = DB::select($query, [
            'ingredient_id' => $ingredient->id
        ]);

        // Map to nested JSON format expected by frontend
        $ingredientNutrients = array_map(function ($ingredientNutrient) {
            return [
                'id' => 0,
                'nutrient_id' => $ingredientNutrient->nutrient_id,
                'amount_per_100g' => $ingredientNutrient->amount_per_100g,
                'nutrient' => [
                    'id' => $ingredientNutrient->nutrient_id,
                    'display_name' => $ingredientNutrient->display_name,
                    'unit_id' => $ingredientNutrient->unit_id,
                    'nutrient_category_id' => $ingredientNutrient->nutrient_category_id,
                    'unit' => [
                        'id' => $ingredientNutrient->unit_id,
                        'name' => $ingredientNutrient->unit_name
                    ]
                ]
            ];
        }, $rawIngredientNutrients);

        $ingredient->load([
            'ingredientCategory:id,name',
            'customUnits:id,name,seq_num,ingredient_id,custom_unit_amount,custom_mass_amount,custom_mass_unit_id,custom_grams',
        ])

        return Inertia::render('Ingredients/Create', [
            'cloned_from_ingredient' => [
                'id' => $ingredient['id'],
                'name' => $ingredient['name'],
                'ingredient_category_id' => $ingredient['ingredient_category_id'],
                'ingredient_category' => $ingredient['ingredientCategory'],
                'ingredient_nutrients' => $ingredientNutrients,
                'density_mass_unit_id' => $ingredient['density_mass_unit_id'],
                'density_mass_amount' => $ingredient['density_mass_amount'],
                'density_volume_unit_id' => $ingredient['density_volume_unit_id'],
                'density_volume_amount' => $ingredient['density_volume_amount'],
                'density_g_ml' => $ingredient['density_g_ml'],
                'custom_units' => $ingredient['customUnits'],
            ],
            'ingredient_categories' => IngredientCategory::getWithNameSorted(),
            'nutrient_categories' => NutrientCategory::getWithName(),
            'units' => Unit::getMassAndVolumeUnits(),
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
            'can_edit' => $user ? $user->can('update', $ingredient) : false,
            'can_clone' => $user ? $user->can('clone', $ingredient) : false,
            'can_delete' => $user ? $user->can('delete', $ingredient) : false,
            'can_create' => $user ? $user->can('create', Ingredient::class) : false
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ingredient $ingredient)
    {
        $user = Auth::user();
        $userId = $user ? $user->id : null;

        return Inertia::render('Ingredients/Edit', [
            'ingredient' => $ingredient->withCategoryUnitsNutrientsAndMeal(),
            'ingredient_categories' => IngredientCategory::getWithNameSorted(),
            'nutrient_categories' => NutrientCategory::getWithName(),
            'units' => Unit::getForIngredient($ingredient),
            'can_view' => $user ? $user->can('view', $ingredient) : false,
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
