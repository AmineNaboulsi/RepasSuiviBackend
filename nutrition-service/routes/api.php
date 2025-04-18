<?php

use App\Http\Controllers\NutritionGoalsController;
use App\Http\Controllers\WeightRecordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



// Route::middleware('redis.auth')->group(function () {
    Route::apiResource('weight-records' , WeightRecordController::class);
    Route::apiResource('nutritiongoeals', NutritionGoalsController::class);
    // Route::apiResource('meal-plans', 'App\Http\Controllers\MealPlanController');
    // Route::apiResource('meal-plan-items', 'App\Http\Controllers\MealPlanItemController');
    
// });
