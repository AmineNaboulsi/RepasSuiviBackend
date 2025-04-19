<?php

namespace App\Providers;

use App\Repositories\ExerciseRepository;
use App\Repositories\Interfaces\ExerciseRepositoryInterface;
use App\Repositories\Interfaces\NutritionGoalsRepositoryInterface;
use App\Repositories\Interfaces\WeightRecordRepositoryInterface;
use App\Repositories\NutritionGoalsRepository;
use App\Repositories\WeightRecordRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(NutritionGoalsRepositoryInterface::class, NutritionGoalsRepository::class);
        $this->app->bind(WeightRecordRepositoryInterface::class, WeightRecordRepository::class);
        $this->app->bind(ExerciseRepositoryInterface::class, ExerciseRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
