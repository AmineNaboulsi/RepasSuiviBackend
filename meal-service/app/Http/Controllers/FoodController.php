<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Food\StoreFoodRequest;
use App\Http\Requests\Food\UpdateFoodRequest;
use App\Http\Requests\Food\UplaodImageFoodRequest;
use App\Http\Resources\FoodResource;
use App\Repositories\Interfaces\FoodRepositoryInterface;
use App\Models\Food;
use Exception;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    private $foodRepository;

    public function __construct(FoodRepositoryInterface $foodRepository)
    {
        $this->foodRepository = $foodRepository;
    }

    public function index(Request $request)
    {
        return FoodResource::collection(Food::take(5)->get());
    }

    public function store(StoreFoodRequest $request)
    {
        $this->foodRepository->create($request->validated());
        return response()->json(['message' => 'Food created successfully'], 201);
    }

    public function show($id)
    {
        if(is_numeric($id)){
            $food = Food::find($id);
            if (!$food) {
                return response()->json(['message' => 'Food item not found'], 404);
            }
            return new FoodResource($food);
        } else {
            $foods = Food::where('name', 'like', "%$id%")->take(5)->get();
            if ($foods->isEmpty()) {
                return response()->json(['message' => 'No food(s) items found matching your search'], 404);
            }
            return FoodResource::collection($foods);
        }
    }

    public function update(UpdateFoodRequest $request, $id)
    {
        $food = Food::find($id);
        if (!$food) {
            return response()->json(['message' => 'Food item not found'], 404);
        }
        try {
            $this->foodRepository->update($food, $request->validated());
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
        return response()->json(['message' => 'Food updated successfully']);
    }

    public function uploadImage(UplaodImageFoodRequest $request, $id)
    {
        $food = Food::find($id);
        if (!$food) {
            return response()->json(['message' => 'Food item not found'], 404);
        }
        try {
            $this->foodRepository->updatefoodimage($food, $request->file('image'));

        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
        return response()->json(['message' => 'Image uploaded successfully'], 200);
        
    }
    public function destroy($id)
    {
        try {
            $food = Food::findOrFail($id);
            $this->foodRepository->delete($food);
            return response()->json(['message' => 'Food deleted successfully']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Food item not found'], 404);
        }
    }
}
