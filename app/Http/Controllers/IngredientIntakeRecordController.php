<?php

namespace App\Http\Controllers;

use App\Models\IngredientIntakeRecord;
use App\Http\Requests\StoreIngredientIntakeRecordRequest;
use App\Http\Requests\UpdateIngredientIntakeRecordRequest;
use App\Services\IngredientIntakeRecordService;
use Illuminate\Http\Request;

class IngredientIntakeRecordController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIngredientIntakeRecordRequest $request, IngredientIntakeRecordService $ingredientIntakeRecordService)
    {
        $ingredientIntakeRecordService->storeIngredientIntakeRecords($request->validated(), $request->user()->id);
        return back()->with('message', 'Success! Ingredient intake record created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIngredientIntakeRecordRequest $request, IngredientIntakeRecord $ingredientIntakeRecord, IngredientIntakeRecordService $ingredientIntakeRecordService)
    {
        $ingredientIntakeRecordService->updateIngredientIntakeRecord($request->validated(), $ingredientIntakeRecord);
        return back()->with('message', 'Success! Ingredient intake record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(IngredientIntakeRecord $ingredientIntakeRecord, IngredientIntakeRecordService $ingredientIntakeRecordService)
    {
        $result = $ingredientIntakeRecordService->deleteIngredientIntakeRecord($ingredientIntakeRecord);
        return back()->with('message', $result['message']);
    }
}
