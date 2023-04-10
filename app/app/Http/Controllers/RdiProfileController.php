<?php

namespace App\Http\Controllers;

use App\Models\RdiProfile;
use App\Models\RdiProfileNutrient;
use Illuminate\Http\Request;

class RdiProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('RdiProfiles/Index', [
          'rdiProfile' => RdiProfile::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('RdiProfiles/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate request
        $num_nutrients = Nutrient::count();
        $request->validate([
            'name' => ['required', 'min:1', 'max:500'],
            'rdi_profile_nutrients' => ['required', 'array', 'min:' . $num_nutrients, 'max:' . $num_nutrients,],
            'rdi_profile_nutrients.*.nutrient_id' => ['required', 'distinct', 'integer', 'in:nutrients,id'],
            'rdi_profile_nutrients.*.rdi' => ['required', 'numeric', 'gt:0'],
        ]);

        // Create RdiProfile
        $rdiProfile = RdiProfile::create([
          'name' => $request->name
        ]);

        // Create RdiProfileNutrients
        foreach ($request->rdi_profile_nutrients as $rdiPN) {
            RdiProfileNutrient::create([
                'rdi_profile_id' => $rdiProfile->id,
                'nutrient_id' => $rdiPN['nutrient_id'],
                'rdi' => $rdiPN['rdi']
            ]);
        }

        return Redirect::route('rdi-profiles.index')->with('message', 'Success! RDI profile created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RdiProfile $rdiProfile)
    {
        // Load name, RDI value, and unit (in nutrient’s preferred units) 
        // of each RdiProfileNutrient
        $rdiProfile->load('rdi_profile_nutrients:id,rdi_profile_id,nutrient_id,rdi', 'rdi_profile_nutrients.nutrient:id,display_name,unit_id', 'rdi_profile_nutrients.nutrient.unit:id,name');

        return Inertia::render('RdiProfiles/Show', [
          'rdiProfile' => $rdiProfile
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RdiProfile $rdiProfile)
    {
        // Load name, RDI value, and unit (in nutrient’s preferred units) 
        // of each RdiProfileNutrient
        $rdiProfile->load('rdi_profile_nutrients:id,rdi_profile_id,nutrient_id,rdi', 'rdi_profile_nutrients.nutrient:id,display_name,unit_id', 'rdi_profile_nutrients.nutrient.unit:id,name');

        return Inertia::render('RdiProfiles/Edit', [
          'rdiProfile' => $rdiProfile
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RdiProfile $rdiProfile)
    {
        // Validate request
        $num_nutrients = Nutrient::count();
        $request->validate([
            'name' => ['required', 'min:1', 'max:500'],
            'rdi_profile_nutrients' => ['required', 'array', 'max:' . $num_nutrients,],
            'rdi_profile_nutrients.*.id' => ['required', 'integer', 'in:rdi_profile_nutrients,id'],
            'rdi_profile_nutrients.*.nutrient_id' => ['required', 'distinct', 'integer', 'in:nutrients,id'],
            'rdi_profile_nutrients.*.rdi' => ['required', 'numeric', 'gt:0'],
        ]);

        // Update RdiProfile
        $rdiProfile->update([
          'name' => $request->name
        ]);

        // Update RdiProfileNutrients
        foreach ($request->rdi_profile_nutrients as $rdiPN) {
            $dbRdiPN = RdiProfileNutrient::find($rdiPN[$id]);
            $dbRdiPN->update([
                'rdi_profile_id' => $rdiProfile->id,
                'nutrient_id' => $rdiPN['nutrient_id'],
                'rdi' => $rdiPN['rdi']
            ]);
        }

        return Redirect::route('rdi-profiles.index')->with('message', 'Success! RDI profile updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RdiProfile $rdiProfile)
    {
        //
    }
}
