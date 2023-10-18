<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\IngredientNutrient;
use App\Models\IngredientCategory;
use App\Models\NutrientCategory;
use App\Models\Nutrient;
use App\Models\IntakeGuideline;
use App\Http\Requests\IngredientStoreRequest;
use App\Http\Requests\IngredientUpdateRequest;

use Illuminate\Http\Request;
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
        $this->authorize('viewAny', Ingredient::class);
        $user = Auth::user();

        return Inertia::render('Ingredients/Index', [
            'user_ingredients' => Auth::user() ? Ingredient::where('user_id', Auth::user()->id)
                ->with('ingredient_category:id,name')
                ->get(['id', 'name', 'ingredient_category_id']) : [],
            'ingredients' => Ingredient::where('user_id', null)
                ->with('ingredient_category:id,name')
                ->get(['id', 'name', 'ingredient_category_id']),
            'ingredient_categories' => IngredientCategory::orderBy('name', 'asc')->get(['id', 'name']),
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
            'precision',
            'display_order_id'
        ]);
        $nutrients->load('unit:id,name');

        return Inertia::render('Ingredients/Create', [
            'ingredient' => [
                'id' => null,
                'name' => "",
                'ingredient_category_id' => null,
                'density_g_per_ml' => null,
                'ingredient_nutrients' => $nutrients->map(fn($nutrient) => [
                    'id' => 0,
                    'nutrient_id' => $nutrient->id,
                    'amount_per_100g' => 0.0,
                    'nutrient' => $nutrient
                ])
            ],
            'ingredients' => Ingredient::where('user_id', null)
            ->orWhere('user_id', $user ? $user->id : 0)
            ->get(['id', 'name', 'ingredient_category_id']),
            'ingredient_categories' => IngredientCategory::orderBy('name', 'asc')->get(['id', 'name']),
            'nutrient_categories' => NutrientCategory::all(['id', 'name']),
            'clone' => false,
            'can_view' => false,  // only relevant for clone
            'can_create' => $user ? $user->can('create', Ingredient::class) : false
        ]);
    }

    /**
     * Like create, but form prefilled with an existing resource's values
     */
    public function clone(Ingredient $ingredient)
    {
        $this->authorize('clone', $ingredient);
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

        $raw_ingredient_nutrients = DB::select($query, [
            'ingredient_id' => $ingredient->id
        ]);

        // Map to nested JSON format expected by frontend
        $ingredient_nutrients = array_map(function ($ingredient_nutrient) {
            return [
                'id' => 0,
                'nutrient_id' => $ingredient_nutrient->nutrient_id,
                'amount_per_100g' => $ingredient_nutrient->amount_per_100g,
                'nutrient' => [
                    'id' => $ingredient_nutrient->nutrient_id,
                    'display_name' => $ingredient_nutrient->display_name,
                    'unit_id' => $ingredient_nutrient->unit_id,
                    'nutrient_category_id' => $ingredient_nutrient->nutrient_category_id,
                    'unit' => [
                        'id' => $ingredient_nutrient->unit_id,
                        'name' => $ingredient_nutrient->unit_name
                    ]
                ]
            ];
        }, $raw_ingredient_nutrients);

        return Inertia::render('Ingredients/Create', [
            'ingredient' => [
                'id' => $ingredient['id'],
                'name' => $ingredient['name'],
                'ingredient_category_id' => $ingredient['ingredient_category_id'],
                'density_g_per_ml' => $ingredient['density_g_per_ml'],
                'ingredient_nutrients' => $ingredient_nutrients
            ],
            'ingredients' => Ingredient::where('user_id', null)
            ->orWhere('user_id', $user ? $user->id : 0)
            ->get(['id', 'name', 'ingredient_category_id']),
            'ingredient_categories' => IngredientCategory::orderBy('name', 'asc')->get(['id', 'name']),
            'nutrient_categories' => NutrientCategory::all(['id', 'name']),
            'clone' => true,
            'can_view' => $user ? $user->can('view', $ingredient) : false,
            'can_create' => $user ? $user->can('create', Ingredient::class) : false
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(IngredientStoreRequest $request)
    {
        $validated = $request->safe()->only([
            'name',
            'ingredient_category_id',
            'density_g_per_ml'
        ]);

        // Create ingredient
        $ingredient = Ingredient::create([
            'name' => $validated['name'],
            'fdc_id' => null,
            'ingredient_category_id' => $validated['ingredient_category_id'],
            'density_g_per_ml' => $validated['density_g_per_ml'],
            'user_id' => $request->user()->id
        ]);

        // Create ingredient's nutrients
        foreach ($request['ingredient_nutrients'] as $in) {
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

        $ingredient->load('ingredient_category:id,name', 'meal:id,name');

        return Inertia::render('Ingredients/Show', [
            'ingredient' => $ingredient->only([
                'id',
                'name',
                'ingredient_category_id',
                'ingredient_category',
                'density_g_per_ml',
                'meal_id',
                'meal'
            ]),
            'has_ingredient_nutrients' => count($ingredient->ingredient_nutrients) > 0,
            'nutrient_profiles' => NutrientProfileController::getNutrientProfilesOfIngredient($ingredient->id),
            'intake_guidelines' => IntakeGuideline::where('user_id', null)
            ->orWhere('user_id', $user ? $user->id : 0)
            ->orderBy('id', 'asc')
            ->get(['id', 'name']),
            'ingredients' => Ingredient::where('user_id', null)
            ->orWhere('user_id', $user ? $user->id : 0)
            ->get(['id', 'name', 'ingredient_category_id']),
            'nutrient_categories' => NutrientCategory::all(['id', 'name']),
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
            'ingredient_nutrients.nutrient:id,display_name,unit_id,nutrient_category_id,precision,display_order_id',
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
            'ingredients' => Ingredient::where('user_id', null)
            ->orWhere('user_id', $user ? $user->id : 0)
            ->get(['id', 'name', 'ingredient_category_id']),
            'ingredient_categories' => IngredientCategory::orderBy('name', 'asc')->get(['id', 'name']),
            'nutrient_categories' => NutrientCategory::all(['id', 'name']),
            'can_view' => $user ? $user->can('view', $ingredient) : false,
            'can_clone' => $user ? $user->can('clone', $ingredient) : false,
            'can_delete' => $user ? $user->can('delete', $ingredient) : false,
            'can_create' => $user ? $user->can('create', Ingredient::class) : false,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(IngredientUpdateRequest $request, Ingredient $ingredient)
    {
        $validated = $request->safe()->only([
            'name',
            'ingredient_category_id',
            'density_g_per_ml'
        ]);

        // Update ingredient
        $ingredient->update([
            'name' => $request['name'],
            'ingredient_category_id' => $request['ingredient_category_id'],
            'density_g_per_ml' => $request['density_g_per_ml'],
        ]);

        // Update ingredient's nutrients
        foreach ($request['ingredient_nutrients'] as $ing) {
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
                $ingredientCategory = IngredientCategory::find($country_id);
                // Preserve "Other" IngredientCategory even if all ingredients are deleted
                if ($ingredientCategory && ($ingredientCategory->name !== IngredientCategory::$OTHER_CATEGORY_NAME)) {
                    $ingredientCategory->delete();
                }
            }
            return Redirect::route('ingredients.index')->with('message', 'Success! Ingredient deleted successfully.');
        }
        return Redirect::route('ingredients.index')->with('message', 'Failed to delete ingredient.');
    }

}
