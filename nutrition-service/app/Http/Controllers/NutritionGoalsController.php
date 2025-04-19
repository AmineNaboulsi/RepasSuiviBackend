<?php

namespace App\Http\Controllers;

use App\Models\NutritionGoals;
use App\Http\Requests\StoreNutritionGoalsRequest;
use App\Http\Requests\UpdateNutritionGoalsRequest;
use App\Repositories\NutritionGoalsRepository;
use Illuminate\Http\Request;

class NutritionGoalsController extends Controller
{
    protected $nutritionGoalsRepository;

    public function __construct(NutritionGoalsRepository $nutritionGoalsRepository)
    {
        $this->nutritionGoalsRepository = $nutritionGoalsRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = $request->userId;
        $goals = $this->nutritionGoalsRepository->getAllById($userId, $request->date);
        return $goals;
    }
  

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNutritionGoalsRequest $request)
    {
        $nutitiongoals = $request->validated();
        $nutitiongoals['user_id'] = $request->userId;
         try{
             return $this->nutritionGoalsRepository->create($nutitiongoals);
         }catch(\Exception $e){
            return response()->json(['error' => 'A nutrition goal already exists within this date range.'], 400);
         }
    }

    // /**
    //  * Display the specified resource.
    //  */
    // public function show($id)
    // {
    //     return $this->nutritionGoalsRepository->getById($id);
    // }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return $this->nutritionGoalsRepository->delete($id);
    }

    /**
     * Get goals for a specific user
     */
    public function getUserGoals($userId)
    {
        return $this->nutritionGoalsRepository->getUserGoals($userId);
    }

    /**
     * Get current active goal for a user
     */
    public function getCurrentGoal($userId)
    {
        return $this->nutritionGoalsRepository->getCurrentGoal($userId);
    }
}
