<?php

namespace App\Http\Controllers;

use App\Models\RdiProfile;
use App\Models\RdiProfileNutrient;
use App\Models\NutrientCategory;
use App\Models\Nutrient;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class RdiProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', RdiProfile::class);
        $user = Auth::user();

        return Inertia::render('RdiProfiles/Index', [
            'rdi_profiles' => RdiProfile::where('user_id', null)
            ->orWhere('user_id', $user ? $user->id : 0)
            ->get(['id', 'name'])
            ->map(fn($profile) => [
                'id' => $profile->id,
                'name' => $profile->name,
                'can_edit' => $user ? $user->can('update', $profile) : false,
                'can_delete' => $user ? $user->can('delete', $profile) : false
            ]),
            'can_create' => $user ? $user->can('create', RdiProfile::class) : false,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', RdiProfile::class);
        $user = Auth::user();

        $nutrients = Nutrient::orderBy('display_order_id', 'asc')->get([
            'id',
            'display_name',
            'unit_id',
            'nutrient_category_id',
            'display_order_id'
        ]);
        $nutrients->load('unit:id,name');

        return Inertia::render('RdiProfiles/Create', [
            'rdi_profile' => [
                'id' => null,
                'name' => "",
                'rdi_profile_nutrients' => $nutrients->map(fn($nutrient) => [
                    'id' => 0,
                    'rdi_profile_id' => 0,
                    'nutrient_id' => $nutrient->id,
                    'rdi' => 0.0,
                    'nutrient' => $nutrient,
                ])
            ],
            'rdi_profiles' => RdiProfile::where('user_id', null)
            ->orWhere('user_id', $user ? $user->id : 0)
            ->get(['id', 'name']),
            'nutrient_categories' => NutrientCategory::all(['id', 'name']),
            'clone' => false,
            'can_view' => false,  // only relevant for clone
            'can_create' => $user ? $user->can('create', RdiProfile::class) : false,
        ]);
    }

    /**
     * Show the form for cloning an existing resource.
     */
    public function clone(RdiProfile $rdiProfile)
    {
        $this->authorize('clone', $rdiProfile);
        $user = Auth::user();

        // The long query is to ensure rdi_profile_nutrients are ordered by
        // nutrients.display_order_id
        $rdiProfile->load([
            'rdi_profile_nutrients' => function($query) {
                $query->select([
                    'rdi_profile_nutrients.id',
                    'rdi_profile_nutrients.rdi_profile_id',
                    'rdi_profile_nutrients.nutrient_id',
                    'rdi_profile_nutrients.rdi'
                ])
                ->join('nutrients', 'rdi_profile_nutrients.nutrient_id', '=', 'nutrients.id')
                ->orderBy('nutrients.display_order_id', 'asc');
            },
            'rdi_profile_nutrients.nutrient:id,display_name,unit_id,nutrient_category_id,display_order_id',
            'rdi_profile_nutrients.nutrient.unit:id,name'
        ]);

        return Inertia::render('RdiProfiles/Create', [
            'rdi_profile' => $rdiProfile->only(['id', 'name', 'rdi_profile_nutrients']),
            'rdi_profiles' => RdiProfile::where('user_id', null)
            ->orWhere('user_id', $user ? $user->id : 0)
            ->get(['id', 'name']),
            'nutrient_categories' => NutrientCategory::all(['id', 'name']),
            'clone' => true,
            'can_view' => $user ? $user->can('view', $rdiProfile) : false,
            'can_create' => $user ? $user->can('create', RdiProfile::class) : false,
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Meal::class);
        $this->validateStoreOrUpdateRequest($request);

        // Create RdiProfile
        $rdiProfile = RdiProfile::create([
            'name' => $request->name,
            'user_id' => $request->user()->id
        ]);

        // Create RdiProfileNutrients
        foreach ($request->rdi_profile_nutrients as $rdiPN) {
            RdiProfileNutrient::create([
                'rdi_profile_id' => $rdiProfile->id,
                'nutrient_id' => $rdiPN['nutrient_id'],
                'rdi' => $rdiPN['rdi']
            ]);
        }

        return Redirect::route('rdi-profiles.show', $rdiProfile->id)->with('message', 'Success! RDI profile created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RdiProfile $rdiProfile)
    {
        $this->authorize('view', $rdiProfile);
        $user = Auth::user();

        // The long query is to ensure rdi_profile_nutrients are ordered by
        // nutrients.display_order_id
        $rdiProfile->load([
            'rdi_profile_nutrients' => function($query) {
                $query->select([
                    'rdi_profile_nutrients.id',
                    'rdi_profile_nutrients.rdi_profile_id',
                    'rdi_profile_nutrients.nutrient_id',
                    'rdi_profile_nutrients.rdi'
                ])
                ->join('nutrients', 'rdi_profile_nutrients.nutrient_id', '=', 'nutrients.id')
                ->orderBy('nutrients.display_order_id', 'asc');
            },
            'rdi_profile_nutrients.nutrient:id,display_name,unit_id,nutrient_category_id,display_order_id',
            'rdi_profile_nutrients.nutrient.unit:id,name'
        ]);

        return Inertia::render('RdiProfiles/Show', [
            'rdi_profile' => $rdiProfile->only(['id', 'name', 'rdi_profile_nutrients']),
            'rdi_profiles' => RdiProfile::where('user_id', null)
            ->orWhere('user_id', $user ? $user->id : 0)
            ->get(['id', 'name']),
            'nutrient_categories' => NutrientCategory::all(['id', 'name']),
            'can_edit' => $user ? $user->can('update', $rdiProfile) : false,
            'can_clone' => $user ? $user->can('clone', $rdiProfile) : false,
            'can_delete' => $user ? $user->can('delete', $rdiProfile) : false,
            'can_create' => $user ? $user->can('create', RdiProfile::class) : false,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RdiProfile $rdiProfile)
    {
        $this->authorize('update', $rdiProfile);
        $user = Auth::user();

        // The long query is to ensure rdi_profile_nutrients are ordered by
        // nutrients.display_order_id
        $rdiProfile->load([
            'rdi_profile_nutrients' => function($query) {
                $query->select([
                    'rdi_profile_nutrients.id',
                    'rdi_profile_nutrients.rdi_profile_id',
                    'rdi_profile_nutrients.nutrient_id',
                    'rdi_profile_nutrients.rdi'
                ])
                ->join('nutrients', 'rdi_profile_nutrients.nutrient_id', '=', 'nutrients.id')
                ->orderBy('nutrients.display_order_id', 'asc');
            },
            'rdi_profile_nutrients.nutrient:id,display_name,unit_id,nutrient_category_id,display_order_id',
            'rdi_profile_nutrients.nutrient.unit:id,name'
        ]);

        return Inertia::render('RdiProfiles/Edit', [
            'rdi_profile' => $rdiProfile->only(['id', 'name', 'rdi_profile_nutrients']),
            'rdi_profiles' => RdiProfile::where('user_id', null)
            ->orWhere('user_id', $user ? $user->id : 0)
            ->get(['id', 'name']),
            'nutrient_categories' => NutrientCategory::all(['id', 'name']),
            'can_view' => $user ? $user->can('view', $rdiProfile) : false,
            'can_clone' => $user ? $user->can('clone', $rdiProfile) : false,
            'can_delete' => $user ? $user->can('delete', $rdiProfile) : false,
            'can_create' => $user ? $user->can('create', RdiProfile::class) : false,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RdiProfile $rdiProfile)
    {
        $this->authorize('update', $rdiProfile);
        $user = Auth::user();

        // Update RdiProfile
        $rdiProfile->update([
            'name' => $request->name
        ]);

        // Update RdiProfileNutrients
        foreach ($request->rdi_profile_nutrients as $rdiPN) {
            $dbRdiPN = RdiProfileNutrient::find($rdiPN['id']);
            if (is_null($dbRdiPN)) continue;
            $dbRdiPN->update([
                'rdi_profile_id' => $rdiProfile->id,
                'nutrient_id' => $rdiPN['nutrient_id'],
                'rdi' => $rdiPN['rdi']
            ]);
        }

        return Redirect::route('rdi-profiles.show', $rdiProfile->id)->with('message', 'Success! RDI profile updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RdiProfile $rdiProfile)
    {
        $this->authorize('delete', $rdiProfile);

        if ($rdiProfile) {
            $rdiProfile->delete();
            return Redirect::route('rdi-profiles.index')->with('message', 'Success! RDI profile deleted successfully.');
        }
        return Redirect::route('rdi-profiles.index')->with('message', 'Failed to delete RDI profile.');
    }

    private function validateStoreOrUpdateRequest(Request $request) {
        $num_nutrients = Nutrient::count();
        $request->validate([
            'name' => ['required', 'min:1', 'max:500'],
            'rdi_profile_nutrients' => ['required', 'array', 'min:' . $num_nutrients, 'max:' . $num_nutrients],
            'rdi_profile_nutrients.*.id' => ['required', 'integer'],
            'rdi_profile_nutrients.*.nutrient_id' => ['required', 'distinct', 'integer', 'exists:nutrients,id'],
            'rdi_profile_nutrients.*.rdi' => ['required', 'numeric', 'gte:0'],
        ]);
    }

}
