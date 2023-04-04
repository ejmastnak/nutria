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

    /**
     * Incoming request takes the form
     *
     * {
     *   "name": "Foo",
     *   "nutrients": [
     *     {
     *       "nutrient_id": 0,
     *       "rdi": 0.0
     *     }
     *   ]
     * }
     *
     * Validation checks that:
     *
     * - name is a string with sane min and max length.
     * - nutrients is an array and contains exactly one item for each record in nutrients table
     * - nutrients.*.nutrient_id is present in nutrients,id
     * - nutrients.*.rdi is a positive float
     *      
     */
    public function validateStoreOrUpdateRequest(Request $request) {
        $num_nutrients = Nutrient::count();
        $request->validate([
            'name' => ['required', 'min:1', 'max:500'],
            'nutrients' => ['required', 'array', 'min:' . $num_nutrients, 'max:' . $num_nutrients,],
            'nutrients.*.nutrient_id' => ['required', 'distinct', 'integer', 'in:nutrients,id'],
            'nutrients.*.rdi' => ['required', 'numeric', 'gt:0'],
        ]);
    }

}
