<?php

namespace App\Http\Controllers;

use App\Models\FoodIntakeRecord;
use App\Http\Requests\StoreIngredientIntakeRecordRequest;
use App\Http\Requests\StoreManyIngredientIntakeRecordsRequest;
use App\Http\Requests\UpdateIngredientIntakeRecordRequest;
use App\Http\Requests\StoreMealIntakeRecordRequest;
use App\Http\Requests\StoreManyMealIntakeRecordsRequest;
use App\Http\Requests\UpdateMealIntakeRecordRequest;
use App\Services\FoodIntakeRecordService;
use Illuminate\Http\Request;

class FoodIntakeRecordController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function storeIngredientIntakeRecord(StoreIngredientIntakeRecordRequest $request, FoodIntakeRecordService $foodIntakeRecordService)
    {
        $this->authorize('create', FoodIntakeRecord::class);
        $foodIntakeRecordService->storeIngredientIntakeRecord($request->validated(), $request->user()->id);
        return back()->with('message', 'Success! Ingredient intake record created successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeManyIngredientIntakeRecords(StoreManyIngredientIntakeRecordsRequest $request, FoodIntakeRecordService $foodIntakeRecordService)
    {
        $this->authorize('createMany', FoodIntakeRecord::class);
        $foodIntakeRecordService->storeManyIngredientIntakeRecords($request->validated(), $request->user()->id);
        return back()->with('message', 'Success! Ingredient intake records created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateIngredientIntakeRecord(UpdateIngredientIntakeRecordRequest $request, FoodIntakeRecord $foodIntakeRecord, FoodIntakeRecordService $foodIntakeRecordService)
    {
        $this->authorize('update', $foodIntakeRecord);
        $foodIntakeRecordService->updateIngredientIntakeRecord($request->validated(), $foodIntakeRecord);
        return back()->with('message', 'Success! Ingredient intake record updated successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeMealIntakeRecord(StoreMealIntakeRecordRequest $request, FoodIntakeRecordService $foodIntakeRecordService)
    {
        $this->authorize('create', FoodIntakeRecord::class);
        $foodIntakeRecordService->storeMealIntakeRecord($request->validated(), $request->user()->id);
        return back()->with('message', 'Success! Meal intake record created successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeManyMealIntakeRecords(StoreManyMealIntakeRecordsRequest $request, FoodIntakeRecordService $foodIntakeRecordService)
    {
        $this->authorize('createMany', FoodIntakeRecord::class);
        $foodIntakeRecordService->storeManyMealIntakeRecords($request->validated(), $request->user()->id);
        return back()->with('message', 'Success! Meal intake records created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateMealIntakeRecord(UpdateMealIntakeRecordRequest $request, FoodIntakeRecord $foodIntakeRecord, FoodIntakeRecordService $foodIntakeRecordService)
    {
        $this->authorize('update', $foodIntakeRecord);
        $foodIntakeRecordService->updateMealIntakeRecord($request->validated(), $foodIntakeRecord);
        return back()->with('message', 'Success! Meal intake record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FoodIntakeRecord $foodIntakeRecord, FoodIntakeRecordService $foodIntakeRecordService)
    {
        $this->authorize('delete', $foodIntakeRecord);
        $result = $foodIntakeRecordService->deleteFoodIntakeRecord($foodIntakeRecord);
        return back()->with('message', $result['message']);
    }
}
