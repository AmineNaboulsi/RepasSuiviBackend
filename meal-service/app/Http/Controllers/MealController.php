<?php

namespace App\Http\Controllers;

use App\Http\Requests\Food\getCaloriesTrend;
use App\Http\Requests\Meal\StoreMealRequest;
use App\Http\Requests\Meal\UpdateMealRequest;
use App\Models\Meal;
use App\Repositories\MealRepository;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\MealResource;
use App\Http\Resources\mealsTrends;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class MealController extends Controller
{
    protected $mealRepository;

    public function __construct(MealRepository $mealRepository)
    {
        $this->mealRepository = $mealRepository;
    }

    public function index(Request $request)
    {
        $userId = $request->userId;
        $meals = Meal::with('foods')->where('user_id' , $userId)->get();
        // return response()->json($meals);
        return response()->json(MealResource::collectionGroupedByDate($meals));
    }

    public function store(StoreMealRequest $request): JsonResponse
    {
        try{
            $mealdata = $request->validated();
            
            $userId = $request->userId;
            $mealdata['meal']['user_id'] = $userId;
            $meal = $this->mealRepository->create($mealdata);
            return response()->json(['message' => 'Meal created successfully', 'meal' => $meal], 201);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
    
  
    public function show(Meal $meal)
    {
        $meal = $this->mealRepository->getById($meal->id);
        return new MealResource($meal);
    }

    public function getCaloroysTrend(getCaloriesTrend $request){
        try{
            $request->validated();
            $userId = $request->userId;
            $meals = $this->mealRepository->getCaloroysTrendbyDate($request->date , $userId);
            return response()->json(
                new mealsTrends($meals) 
                // $meals
            );
        }catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ], 400);
        }
    }
    public function update(UpdateMealRequest $request, Meal $meal): JsonResponse
    {
        try {
            $this->authorize('update', $meal);
        } catch (AuthorizationException $e) {
            return response()->json([
                'message' => 'You are not authorized to update this meal',
                'error' => $e->getMessage()
            ], 403);
        }

        $updatedMeal = $this->mealRepository->update($meal, $request->validated());
        return response()->json(['message' => 'Meal updated successfully', 'meal' => $updatedMeal]);
    }

    
 
    public function destroy($id)    
    {
        try {
            $meal = Meal::find($id);
            if(!$meal) {
                return response()->json(['message' => 'Meal not found'], 404);
            }
            $this->mealRepository->delete($meal);
            return response()->json(['message' => 'Meal deleted successfully']);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
       
    }
}
