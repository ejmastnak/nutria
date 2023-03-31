<?php

namespace App\Http\Controllers;

use App\Models\FoodList;
use Illuminate\Http\Request;
use Inertia\Intertia;

class FoodListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('FoodLists/Index', [
          'foodLists' => FoodList::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('FoodLists/Create');
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
    public function show(FoodList $foodList)
    {
        return Inertia::render('FoodLists/Show', [
          'foodLists' => FoodList::all()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FoodList $foodList)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FoodList $foodList)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FoodList $foodList)
    {
        //
    }
}
