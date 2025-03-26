<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Http\Requests\Food\StoreFoodRequest;
use App\Http\Requests\Food\UpdateFoodRequest;
use App\Http\Serivces\FoodService;

class FoodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return response()->json([
            'test' => 'test'
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFoodRequest $request)
    {
        $validatedData = $request->validated();
        
        $food = new Food([
            'name' => $validatedData['name'],
            'calories' => $validatedData['calories'],
            'proteins' => $validatedData['proteins'],
            'glucides' => $validatedData['glucides'],
            'lipides' => $validatedData['lipides'],
            'category' => $validatedData['category']
        ]);
        $ServiceFood = new FoodService();;
        return $ServiceFood->store($food);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFoodRequest $request, Food $food)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Food $food)
    {
        //
    }
}
