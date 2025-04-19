<?php

use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\NutritionGoalsController;
use App\Http\Controllers\WeightRecordController;
use App\Models\Exercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::apiResource('weight-records' , WeightRecordController::class);
Route::apiResource('nutritiongoeals', NutritionGoalsController::class);
Route::apiResource('exercises', ExerciseController::class);

