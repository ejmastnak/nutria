<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\IngredientNutrient;
use App\Models\IngredientCategory;
use App\Models\NutrientCategory;
use App\Models\Nutrient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class IngredientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Ingredient::class);
        $user = Auth::user();

        return Inertia::render('Ingredients/Index', [
            'user_ingredients' => Auth::user() ? Ingredient::where('user_id', Auth::user()->id)
                ->with('ingredient_category:id,name')
                ->get(['id', 'name', 'ingredient_category_id']) : [],
            'ingredients' => Ingredient::where('user_id', null)
                ->with('ingredient_category:id,name')
                ->get(['id', 'name', 'ingredient_category_id']),
            'ingredient_categories' => IngredientCategory::all(['id', 'name']),
            'can_create' => $user ? $user->can('create', Ingredient::class) : false,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Ingredient::class);
        $user = Auth::user();

        $nutrients = Nutrient::orderBy('display_order_id', 'asc')->get([
            'id',
            'display_name',
            'unit_id',
            'nutrient_category_id',
            'display_order_id'
        ]);
        $nutrients->load('unit:id,name');

        return Inertia::render('Ingredients/Create', [
            'ingredient' => [
                'id' => null,
                'name' => "",
                'ingredient_category_id' => null,
                'ingredient_category' => null,
                'density_g_per_ml' => null,
                'ingredient_nutrients' => $nutrients->map(fn($nutrient) => [
                    'id' => 0,
                    'nutrient_id' => $nutrient->id,
                    'amount_per_100g' => 0.0,
                    'nutrient' => $nutrient,
                    'nutrient_category_id' => $nutrient->nutrient_category_id
                ])
            ],
            'ingredient_categories' => IngredientCategory::all(['id', 'name']),
            'nutrient_categories' => NutrientCategory::all(['id', 'name']),
            'can_create' => $user ? $user->can('create', Ingredient::class) : false,
            'clone' => false
        ]);
    }

    /**
     * Like create, but form prefilled with an existing resource's values
     */
    public function clone(Ingredient $ingredient)
    {
        $this->authorize('clone', $ingredient);
        $user = Auth::user();

        // The long query is to ensure ingredient_nutrients are ordered by
        // nutrients.display_order_id
        $ingredient->load([
            'ingredient_nutrients' => function($query) {
                $query->select([
                    'ingredient_nutrients.id',
                    'ingredient_nutrients.ingredient_id',
                    'ingredient_nutrients.nutrient_id',
                    'ingredient_nutrients.amount_per_100g'
                ])
                ->join('nutrients', 'ingredient_nutrients.nutrient_id', '=', 'nutrients.id')
                ->orderBy('nutrients.display_order_id', 'asc');
            },
            'ingredient_nutrients.nutrient:id,display_name,nutrient_category_id,unit_id',
            'ingredient_nutrients.nutrient.unit:id,name'
        ]);

        return Inertia::render('Ingredients/Create', [
            'ingredient' => [
                'id' => $ingredient['id'],
                'name' => $ingredient['name'],
                'ingredient_category_id' => $ingredient['ingredient_category_id'],
                'ingredient_category' => $ingredient['ingredient_category'],
                'density_g_per_ml' => $ingredient['density_g_per_ml'],
                'ingredient_nutrients' => $ingredient['ingredient_nutrients']
            ],
            'ingredient_categories' => IngredientCategory::all(['id', 'name']),
            'nutrient_categories' => NutrientCategory::all(['id', 'name']),
            'can_create' => $user ? $user->can('create', Ingredient::class) : false,
            'clone' => true
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Ingredient::class);
        $this->validateStoreOrUpdateRequest($request);

        // Create ingredient
        $ingredient = Ingredient::create([
            'name' => $request->name,
            'fdc_id' => $request->fdc_id,
            'ingredient_category_id' => $request->ingredient_category_id,
            'density_g_per_ml' => $request->density_g_per_ml,
            'user_id' => $request->user()->id
        ]);

        // Create ingredient's nutrients
        foreach ($request->ingredient_nutrients as $in) {
            IngredientNutrient::create([
                'ingredient_id' => $ingredient->id,
                'nutrient_id' => $in['nutrient_id'],
                'amount_per_100g' => $in['amount_per_100g'],
            ]);
        }

        return Redirect::route('ingredients.show', $ingredient->id)->with('message', 'Success! Ingredient created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ingredient $ingredient)
    {
        $this->authorize('view', $ingredient);
        $user = Auth::user();

        $ingredient->load('ingredient_category:id,name');

        return Inertia::render('Ingredients/Show', [
            'ingredient' => $ingredient->only([
                'id',
                'name',
                'ingredient_category_id',
                'ingredient_category',
                'density_g_per_ml'
            ]),
            'nutrient_profile' => NutrientProfileController::profileIngredient($ingredient->id),
            'ingredients' => Ingredient::where('user_id', null)
            ->orWhere('user_id', $user ? $user->id : 0)
            ->get(['id', 'name', 'ingredient_category_id']),
            'nutrient_categories' => NutrientCategory::all(['id', 'name']),
            'can_edit' => $user ? $user->can('update', $ingredient) : false,
            'can_clone' => $user ? $user->can('clone', $ingredient) : false,
            'can_delete' => $user ? $user->can('delete', $ingredient) : false,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ingredient $ingredient)
    {
        $this->authorize('update', $ingredient);
        $user = Auth::user();

        // The long query is to ensure ingredient_nutrients are ordered by
        // nutrients.display_order_id
        $ingredient->load([
            'ingredient_nutrients' => function($query) {
                $query->select([
                    'ingredient_nutrients.id',
                    'ingredient_nutrients.ingredient_id',
                    'ingredient_nutrients.nutrient_id',
                    'ingredient_nutrients.amount_per_100g'
                ])
                ->join('nutrients', 'ingredient_nutrients.nutrient_id', '=', 'nutrients.id')
                ->orderBy('nutrients.display_order_id', 'asc');
            },
            'ingredient_nutrients.nutrient:id,display_name,nutrient_category_id,unit_id',
            'ingredient_nutrients.nutrient.unit:id,name'
        ]);

        return Inertia::render('Ingredients/Edit', [
            'ingredient' => $ingredient->only([
                'id',
                'name',
                'ingredient_category_id',
                'density_g_per_ml',
                'ingredient_nutrients'
            ]),
            'ingredient_categories' => IngredientCategory::all(['id', 'name']),
            'nutrient_categories' => NutrientCategory::all(['id', 'name']),
            'can_delete' => $user ? $user->can('delete', $ingredient) : false,
            'can_clone' => $user ? $user->can('clone', $ingredient) : false,
            'can_create' => $user ? $user->can('create', Ingredient::class) : false,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ingredient $ingredient)
    {
        $this->authorize('update', $ingredient);
        $this->validateStoreOrUpdateRequest($request);

        // Update ingredient
        $ingredient->update([
            'name' => $request->name,
            'fdc_id' => $request->fdc_id,
            'ingredient_category_id' => $request->ingredient_category_id,
            'density_g_per_ml' => $request->density_g_per_ml,
        ]);

        // Update ingredient's nutrients
        foreach ($request->ingredient_nutrients as $ing) {
            $dbIng = IngredientNutrient::find($ing['id']);
            if ($dbIng) {
                $dbIng->update(['amount_per_100g' => $ing['amount_per_100g']]);
            }
        }

        return Redirect::route('ingredients.show', $ingredient->id)->with('message', 'Success! Ingredient updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ingredient $ingredient)
    {
        $this->authorize('delete', $ingredient);

        if ($ingredient) {
            $ingredient_category_id = $ingredient->ingredient_category_id;
            $ingredient->delete();
            // Also delete the ingredient's IngredientCategory if there
            // are no remaining ingredients with this category
            if(Ingredient::where('ingredient_category_id', $ingredient_category_id)->doesntExist()) {
                IngredientCategory::find($country_id)->delete();
            }
            return Redirect::route('ingredients.index')->with('message', 'Success! Ingredient deleted successfully.');
        }
        return Redirect::route('ingredients.index')->with('message', 'Failed to delete ingredient.');
    }

    // TODO: switch to this
    // 'ingredient_nutrients' => ['required', 'array', 'min:' . $num_nutrients, 'max:' . $num_nutrients],
    private function validateStoreOrUpdateRequest($request) {
        $num_nutrients = Nutrient::count();
        $request->validate([
            'name' => ['required', 'min:1', 'max:500'],
            'ingredient_category_id' => ['required', 'integer', 'exists:ingredient_categories,id'],
            'density_g_per_ml' => ['nullable', 'numeric', 'gt:0'],
            'ingredient_nutrients' => ['required', 'array', 'max:' . $num_nutrients],
            'ingredient_nutrients.*.id' => ['required', 'integer'],
            'ingredient_nutrients.*.nutrient_id' => ['required', 'distinct', 'integer', 'exists:nutrients,id'],
            'ingredient_nutrients.*.amount_per_100g' => ['required', 'numeric', 'gte:0'],
        ]);
    }

}
