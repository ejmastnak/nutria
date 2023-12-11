<?php

namespace App\Http\Controllers;

use App\Models\FoodListIntakeRecord;
use App\Http\Requests\StoreFoodListIntakeRecordRequest;
use App\Http\Requests\UpdateFoodListIntakeRecordRequest;
use App\Services\FoodListIntakeRecordService;
use Illuminate\Http\Request;

class FoodListIntakeRecordController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFoodListIntakeRecordRequest $request, FoodListIntakeRecordService $foodListIntakeRecordService)
    {
        $foodListIntakeRecordService->storeFoodListIntakeRecords($request->validated(), $request->user()->id);
        return back()->with('message', 'Success! Food list intake record(s) created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFoodListIntakeRecordRequest $request, FoodListIntakeRecord $foodListIntakeRecord, FoodListIntakeRecordService $foodListIntakeRecordService)
    {
        $foodListIntakeRecordService->updateFoodListIntakeRecord($request->validated(), $foodListIntakeRecord);
        return back()->with('message', 'Success! Food list intake record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FoodListIntakeRecord $foodListIntakeRecord, FoodListIntakeRecordService $foodListIntakeRecordService)
    {
        $result = $foodListIntakeRecordService->deleteFoodListIntakeRecord($foodListIntakeRecord);
        return back()->with('message', $result['message']);
    }
}
