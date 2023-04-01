<?php

namespace App\Http\Controllers;

use App\Models\RdiProfile;
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RdiProfile $rdiProfile)
    {
        //
    }
}
