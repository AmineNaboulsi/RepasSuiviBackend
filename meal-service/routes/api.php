<?php

use App\Http\Controllers\FoodController;
use App\Http\Controllers\MealController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::apiResource('foods', FoodController::class);
Route::post('food/{food:id}/upload', [FoodController::class, 'uploadImage']);
Route::apiResource('meals', MealController::class);
