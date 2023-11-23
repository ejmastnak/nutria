<?php

namespace App\Http\Controllers;

use App\Models\BodyWeightRecord;
use App\Http\Requests\StoreBodyWeightRecordRequest;
use App\Http\Requests\UpdateBodyWeightRecordRequest;
use App\Services\BodyWeightRecordService;
use Illuminate\Http\Request;

class BodyWeightRecordController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBodyWeightRecordRequest $request, BodyWeightRecordService $bodyWeightRecordService)
    {
        $bodyWeightRecordService->storeBodyWeightRecord($request->validated(), $request->user()->id);
        return back()->with('message', 'Success! Body weight record created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBodyWeightRecordRequest $request, BodyWeightRecord $bodyWeightRecord, BodyWeightRecordService $bodyWeightRecordService)
    {
        $bodyWeightRecordService->updateBodyWeightRecord($request->validated(), $bodyWeightRecord);
        return back()->with('message', 'Success! Body weight record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BodyWeightRecord $bodyWeightRecord, BodyWeightRecordService $bodyWeightRecordService)
    {
        $result = $bodyWeightRecordService->deleteBodyWeightRecord($bodyWeightRecord);
        return back()->with('message', $result['message']);
    }
}
