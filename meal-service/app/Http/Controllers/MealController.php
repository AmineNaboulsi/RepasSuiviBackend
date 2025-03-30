<?php

namespace App\Http\Controllers;

use App\Http\Requests\Meal\StoreMealRequest;
use App\Http\Requests\Meal\UpdateMealRequest;
use App\Models\Meal;
use App\Repositories\MealRepository;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\MealResource;
use Exception;
use Illuminate\Support\Facades\Http;

class MealController extends Controller
{
    protected $mealRepository;

    public function __construct(MealRepository $mealRepository)
    {
        $this->mealRepository = $mealRepository;
    }

    public function index()
    {
        $meals = Meal::with('foods')->get();
        return response()->json(MealResource::collectionGroupedByDate($meals));
    }

    public function store(StoreMealRequest $request): JsonResponse
    {
        // $response = Http::post('http://cdc/api/resource', [
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        
        // if ($response->successful()) {
        //     $data = $response->json();
        // } else {
        //     $statusCode = $response->status();
        //     $errorMessage = $response->body();
        // }

        $meal = $this->mealRepository->create($request->validated());
        return response()->json(['message' => 'Meal created successfully', 'meal' => $meal], 201);
    }
    
  
    public function show(Meal $meal)
    {
        $meal = $this->mealRepository->getById($meal->id);
        return new MealResource($meal);
    }

    public function update(UpdateMealRequest $request, Meal $meal): JsonResponse
    {
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
