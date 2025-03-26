<?php

namespace App\Http\Serivces;
use App\Http\Controllers\FoodController;
class FoodService
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store($request)
    {
        $this->FoodController->store($request);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($request, $food)
    {
        $this->FoodController->update($request, $food);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($food)
    {
        $this->FoodController->destroy($food);
    }
}