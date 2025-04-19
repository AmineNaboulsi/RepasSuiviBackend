<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWeightRecordRequest;
use App\Http\Requests\UpdateWeightRecordRequest;
use App\Http\Resources\WeightRecordeResource;
use App\Models\WeightRecord;
use App\Repositories\Interfaces\WeightRecordRepositoryInterface;
use App\Repositories\WeightRecordRepository;
use Illuminate\Http\Request;

class WeightRecordController extends Controller
{
    protected $weightRecordRepository;

    public function __construct(WeightRecordRepositoryInterface $weightRecordRepository)
    {
        $this->weightRecordRepository = $weightRecordRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($date = $request->input('date')){
            if($request->input('f')){
                $weightRecords = $this->weightRecordRepository->SearchByDate($request->userId , $date);
                return response()->json(WeightRecordeResource::collection($weightRecords));
            }
            $weightRecords = $this->weightRecordRepository->DateFilter($request->userId , $date);
        }else{
            $weightRecords = $this->weightRecordRepository->getUserWeightRecords($request->userId);
        }
        
        return response()->json(WeightRecordeResource::collection($weightRecords));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWeightRecordRequest $request)
    {
        try{
            $weightData = $request->validated();
        }catch(\Exception $e){
            return response()->json([
                'data' => $e->getMessage()
            ]);
        }

        $weightData['user_id'] = $request->userId;
        $result = $this->weightRecordRepository->create($weightData);
        
        return response()->json([
            'data' => $result
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(WeightRecord $weightRecord)
    {
        $weightRecord = $this->weightRecordRepository->getById($weightRecord->id);
        // $this->authorize('view', $weightRecord);
        return response()->json([
            'message' => 'Weight record retrieved successfully',
            'data' => $weightRecord
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WeightRecord $WeightRecord)
    {
        $weightRecord = WeightRecord::findOrFail($WeightRecord->id);
        if(!$weightRecord)
            return response()->json([
                'message' => 'Weight not found'
            ]);
        $this->weightRecordRepository->delete($weightRecord->id);
        
        return response()->json([
            'message' => 'Weight record deleted successfully'
        ]);
    }
}
