<?php

namespace App\Http\Controllers;

use App\Models\IntakeGuideline;
use App\Models\NutrientCategory;
use App\Models\Nutrient;
use App\Http\Requests\IntakeGuidelineStoreRequest;
use App\Http\Requests\IntakeGuidelineUpdateRequest;
use App\Services\IntakeGuidelineService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

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
    public function store(IntakeGuidelineStoreRequest $request, IntakeGuidelineService $intakeGuidelineService)
    {
        $intakeGuideline = $intakeGuidelineService->storeIntakeGuideline($request->validated(), $request->user()->id);
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
    public function update(IntakeGuidelineUpdateRequest $request, IntakeGuideline $intakeGuideline, IntakeGuidelineService $intakeGuidelineService)
    {
        $intakeGuidelineService->updateIntakeGuideline($request->validated(), $intakeGuideline);
        return Redirect::route('intake-guidelines.show', $intakeGuideline->id)->with('message', 'Success! Intake Guideline updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(IntakeGuideline $intakeGuideline, IntakeGuidelineService $intakeGuidelineService)
    {
        $result = $intakeGuidelineService->deleteIntakeGuideline($intakeGuideline);
        if ($result['success']) return Redirect::route('intake-guidelines.index')->with('message', $result['message']);
        else return Redirect::route('intake-guidelines.index')->with('message', $result['message']);
    }

}
