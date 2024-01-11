<?php

namespace App\Http\Controllers;

use App\Models\MealIntakeRecord;
use App\Http\Requests\StoreMealIntakeRecordRequest;
use App\Http\Requests\StoreManyMealIntakeRecordsRequest;
use App\Http\Requests\UpdateMealIntakeRecordRequest;
use App\Services\MealIntakeRecordService;
use Illuminate\Http\Request;

class MealIntakeRecordController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMealIntakeRecordRequest $request, MealIntakeRecordService $mealIntakeRecordService)
    {
        $mealIntakeRecordService->storeMealIntakeRecord($request->validated(), $request->user()->id);
        return back()->with('message', 'Success! Meal intake record created successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeMany(StoreManyMealIntakeRecordsRequest $request, MealIntakeRecordService $mealIntakeRecordService)
    {
        $mealIntakeRecordService->storeManyMealIntakeRecords($request->validated(), $request->user()->id);
        return back()->with('message', 'Success! Meal intake records created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMealIntakeRecordRequest $request, MealIntakeRecord $mealIntakeRecord, MealIntakeRecordService $mealIntakeRecordService)
    {
        $mealIntakeRecordService->updateMealIntakeRecord($request->validated(), $mealIntakeRecord);
        return back()->with('message', 'Success! Meal intake record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MealIntakeRecord $mealIntakeRecord, MealIntakeRecordService $mealIntakeRecordService)
    {
        $result = $mealIntakeRecordService->deleteMealIntakeRecord($mealIntakeRecord);
        return back()->with('message', $result['message']);
    }
}
