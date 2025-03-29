<?php

namespace App\Providers;

use App\Repositories\FoodRepository;
use App\Repositories\Interfaces\FoodRepositoryInterface;
use App\Repositories\Interfaces\MealRepositoryInterface;
use App\Repositories\MealRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(FoodRepositoryInterface::class, FoodRepository::class);
        $this->app->bind(MealRepositoryInterface::class, MealRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
