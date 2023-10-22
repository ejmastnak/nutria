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
        $user = Auth::user();
        $userId = $user ? $user->id : null;

        return Inertia::render('IntakeGuidelines/Index', [
            'intake_guidelines' => IntakeGuideline::getForUserWithPermissions($userId),
            'can_create' => $user ? $user->can('create', IntakeGuideline::class) : false,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();

        return Inertia::render('IntakeGuidelines/Create', [
            'cloned_from_intake_guideline' => null,
            'nutrients' => Nutrient::getWithUnit(),
            'nutrient_categories' => NutrientCategory::getWithName(),
            'can_create' => $user ? $user->can('create', IntakeGuideline::class) : false,
        ]);
    }

    /**
     * Show the form for cloning an existing resource.
     */
    public function clone(IntakeGuideline $intakeGuideline)
    {
        $user = Auth::user();

        return Inertia::render('IntakeGuidelines/Create', [
            'cloned_from_intake_guideline' => $intakeGuideline->withNutrients(),
            'nutrients' => null,
            'nutrient_categories' => NutrientCategory::getWithName(),
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
        $user = Auth::user();

        return Inertia::render('IntakeGuidelines/Show', [
            'intake_guideline' => $intakeGuideline->withNutrients(),
            'nutrient_categories' => NutrientCategory::getWithName(),
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
        $user = Auth::user();

        return Inertia::render('IntakeGuidelines/Edit', [
            'intake_guideline' => $intakeGuideline->withNutrients(),
            'nutrient_categories' => NutrientCategory::getWithName(),
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
