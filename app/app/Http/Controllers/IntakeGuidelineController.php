<?php

namespace App\Http\Controllers;

use App\Models\IntakeGuideline;
use App\Models\IntakeGuidelineNutrient;
use App\Models\NutrientCategory;
use App\Models\Nutrient;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class IntakeGuidelineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', IntakeGuideline::class);
        $user = Auth::user();

        return Inertia::render('IntakeGuidelines/Index', [
            'intake_guidelines' => IntakeGuideline::where('user_id', null)
            ->orWhere('user_id', $user ? $user->id : 0)
            ->get(['id', 'name'])
            ->map(fn($profile) => [
                'id' => $profile->id,
                'name' => $profile->name,
                'can_edit' => $user ? $user->can('update', $profile) : false,
                'can_delete' => $user ? $user->can('delete', $profile) : false
            ]),
            'can_create' => $user ? $user->can('create', IntakeGuideline::class) : false,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', IntakeGuideline::class);
        $user = Auth::user();

        $nutrients = Nutrient::orderBy('display_order_id', 'asc')->get([
            'id',
            'display_name',
            'unit_id',
            'nutrient_category_id',
            "precision",
            'display_order_id'
        ]);
        $nutrients->load('unit:id,name');

        return Inertia::render('IntakeGuidelines/Create', [
            'intake_guideline' => [
                'id' => null,
                'name' => "",
                'intake_guideline_nutrients' => $nutrients->map(fn($nutrient) => [
                    'id' => 0,
                    'intake_guideline_id' => 0,
                    'nutrient_id' => $nutrient->id,
                    'rdi' => 0.0,
                    'nutrient' => $nutrient,
                ])
            ],
            'intake_guidelines' => IntakeGuideline::where('user_id', null)
            ->orWhere('user_id', $user ? $user->id : 0)
            ->get(['id', 'name']),
            'nutrient_categories' => NutrientCategory::all(['id', 'name']),
            'clone' => false,
            'can_view' => false,  // only relevant for clone
            'can_create' => $user ? $user->can('create', IntakeGuideline::class) : false,
        ]);
    }

    /**
     * Show the form for cloning an existing resource.
     */
    public function clone(IntakeGuideline $intakeGuideline)
    {
        $this->authorize('clone', $intakeGuideline);
        $user = Auth::user();

        // The long query is to ensure intake_guideline_nutrients are ordered by
        // nutrients.display_order_id
        $intakeGuideline->load([
            'intake_guideline_nutrients' => function($query) {
                $query->select([
                    'intake_guideline_nutrients.id',
                    'intake_guideline_nutrients.intake_guideline_id',
                    'intake_guideline_nutrients.nutrient_id',
                    'intake_guideline_nutrients.rdi'
                ])
                ->join('nutrients', 'intake_guideline_nutrients.nutrient_id', '=', 'nutrients.id')
                ->orderBy('nutrients.display_order_id', 'asc');
            },
            'intake_guideline_nutrients.nutrient:id,display_name,unit_id,nutrient_category_id,precision,display_order_id',
            'intake_guideline_nutrients.nutrient.unit:id,name'
        ]);

        return Inertia::render('IntakeGuidelines/Create', [
            'intake_guideline' => $intakeGuideline->only(['id', 'name', 'intake_guideline_nutrients']),
            'intake_guidelines' => IntakeGuideline::where('user_id', null)
            ->orWhere('user_id', $user ? $user->id : 0)
            ->get(['id', 'name']),
            'nutrient_categories' => NutrientCategory::all(['id', 'name']),
            'clone' => true,
            'can_view' => $user ? $user->can('view', $intakeGuideline) : false,
            'can_create' => $user ? $user->can('create', IntakeGuideline::class) : false,
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Meal::class);
        $this->validateStoreOrUpdateRequest($request);

        // Create IntakeGuideline
        $intakeGuideline = IntakeGuideline::create([
            'name' => $request->name,
            'user_id' => $request->user()->id
        ]);

        // Create IntakeGuidelineNutrients
        foreach ($request->intake_guideline_nutrients as $ign) {
            IntakeGuidelineNutrient::create([
                'intake_guideline_id' => $intakeGuideline->id,
                'nutrient_id' => $ign['nutrient_id'],
                'rdi' => $ign['rdi']
            ]);
        }

        return Redirect::route('intake-guidelines.show', $intakeGuideline->id)->with('message', 'Success! Intake Guideline created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(IntakeGuideline $intakeGuideline)
    {
        $this->authorize('view', $intakeGuideline);
        $user = Auth::user();

        // The long query is to ensure intake_guideline_nutrients are ordered by
        // nutrients.display_order_id
        $intakeGuideline->load([
            'intake_guideline_nutrients' => function($query) {
                $query->select([
                    'intake_guideline_nutrients.id',
                    'intake_guideline_nutrients.intake_guideline_id',
                    'intake_guideline_nutrients.nutrient_id',
                    'intake_guideline_nutrients.rdi'
                ])
                ->join('nutrients', 'intake_guideline_nutrients.nutrient_id', '=', 'nutrients.id')
                ->orderBy('nutrients.display_order_id', 'asc');
            },
            'intake_guideline_nutrients.nutrient:id,display_name,unit_id,nutrient_category_id,precision,display_order_id',
            'intake_guideline_nutrients.nutrient.unit:id,name'
        ]);

        return Inertia::render('IntakeGuidelines/Show', [
            'intake_guideline' => $intakeGuideline->only(['id', 'name', 'intake_guideline_nutrients']),
            'intake_guidelines' => IntakeGuideline::where('user_id', null)
            ->orWhere('user_id', $user ? $user->id : 0)
            ->get(['id', 'name']),
            'nutrient_categories' => NutrientCategory::all(['id', 'name']),
            'can_edit' => $user ? $user->can('update', $intakeGuideline) : false,
            'can_clone' => $user ? $user->can('clone', $intakeGuideline) : false,
            'can_delete' => $user ? $user->can('delete', $intakeGuideline) : false,
            'can_create' => $user ? $user->can('create', IntakeGuideline::class) : false,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(IntakeGuideline $intakeGuideline)
    {
        $this->authorize('update', $intakeGuideline);
        $user = Auth::user();

        // The long query is to ensure intake_guideline_nutrients are ordered by
        // nutrients.display_order_id
        $intakeGuideline->load([
            'intake_guideline_nutrients' => function($query) {
                $query->select([
                    'intake_guideline_nutrients.id',
                    'intake_guideline_nutrients.intake_guideline_id',
                    'intake_guideline_nutrients.nutrient_id',
                    'intake_guideline_nutrients.rdi'
                ])
                ->join('nutrients', 'intake_guideline_nutrients.nutrient_id', '=', 'nutrients.id')
                ->orderBy('nutrients.display_order_id', 'asc');
            },
            'intake_guideline_nutrients.nutrient:id,display_name,unit_id,nutrient_category_id,precision,display_order_id',
            'intake_guideline_nutrients.nutrient.unit:id,name'
        ]);

        return Inertia::render('IntakeGuidelines/Edit', [
            'intake_guideline' => $intakeGuideline->only(['id', 'name', 'intake_guideline_nutrients']),
            'intake_guidelines' => IntakeGuideline::where('user_id', null)
            ->orWhere('user_id', $user ? $user->id : 0)
            ->get(['id', 'name']),
            'nutrient_categories' => NutrientCategory::all(['id', 'name']),
            'can_view' => $user ? $user->can('view', $intakeGuideline) : false,
            'can_clone' => $user ? $user->can('clone', $intakeGuideline) : false,
            'can_delete' => $user ? $user->can('delete', $intakeGuideline) : false,
            'can_create' => $user ? $user->can('create', IntakeGuideline::class) : false,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, IntakeGuideline $intakeGuideline)
    {
        $this->authorize('update', $intakeGuideline);
        $user = Auth::user();

        // Update IntakeGuideline
        $intakeGuideline->update([
            'name' => $request->name
        ]);

        // Update IntakeGuidelineNutrients
        foreach ($request->intake_guideline_nutrients as $ign) {
            $dbIGN = IntakeGuidelineNutrient::find($ign['id']);
            if (is_null($dbIGN)) continue;
            $dbIGN->update([
                'intake_guideline_id' => $intakeGuideline->id,
                'nutrient_id' => $ign['nutrient_id'],
                'rdi' => $ign['rdi']
            ]);
        }

        return Redirect::route('intake-guidelines.show', $intakeGuideline->id)->with('message', 'Success! Intake Guideline updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(IntakeGuideline $intakeGuideline)
    {
        $this->authorize('delete', $intakeGuideline);

        if ($intakeGuideline) {
            $intakeGuideline->delete();
            return Redirect::route('intake-guidelines.index')->with('message', 'Success! Intake Guideline deleted successfully.');
        }
        return Redirect::route('intake-guidelines.index')->with('message', 'Failed to delete intake guideline.');
    }

    private function validateStoreOrUpdateRequest(Request $request) {
        $num_nutrients = Nutrient::count();
        $request->validate([
            'name' => ['required', 'min:1', 'max:500'],
            'intake_guideline_nutrients' => ['required', 'array', 'min:' . $num_nutrients, 'max:' . $num_nutrients],
            'intake_guideline_nutrients.*.id' => ['required', 'integer'],
            'intake_guideline_nutrients.*.nutrient_id' => ['required', 'distinct', 'integer', 'exists:nutrients,id'],
            'intake_guideline_nutrients.*.rdi' => ['required', 'numeric', 'gte:0'],
        ]);
    }

}
