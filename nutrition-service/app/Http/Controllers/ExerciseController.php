<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Http\Requests\StoreExerciseRequest;
use App\Http\Requests\UpdateExerciseRequest;
use App\Http\Resources\ExercisesRecource;
use App\Http\Resources\ExerciseWeeksRecource;
use App\Repositories\Interfaces\ExerciseRepositoryInterface;
use Illuminate\Http\Request;

class ExerciseController extends Controller
{
    protected $exerciseRepository;
    public function __construct(ExerciseRepositoryInterface $exerciseRepository)
    {
        $this->exerciseRepository = $exerciseRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = $request->input('userId');
        $date = $request->input('date');
        $filter = $request->input('f');
    
        try {
            if ($filter === 'week') {
                $exercises = $this->exerciseRepository->getAllWeekTrained($userId, $date);
                return response()->json(new ExerciseWeeksRecource($exercises));
            } else {
                $exercises = $this->exerciseRepository->getMonthTrained($userId, $date);
                return response()->json(new ExercisesRecource($exercises));
            }
    
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error retrieving exercises: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExerciseRequest $request)
    {
        try{
            $data = $request->all();
            $data['user_id'] = $request->userId;
            $exercises = $this->exerciseRepository->add_TrainingTime($data);
            return response()->json($exercises);
        }catch(\Exception $e) {
            return response()->json(['error' => 'Error retrieving exercises: ' . $e->getMessage()], 500);
        }

    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExerciseRequest $request, Exercise $exercise)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exercise $exercise)
    {
        //
    }
}
