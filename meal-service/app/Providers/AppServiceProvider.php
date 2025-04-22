<?php

namespace App\Providers;

use App\Repositories\FoodRepository;
use App\Repositories\Interfaces\FoodRepositoryInterface;
use App\Repositories\Interfaces\MealRepositoryInterface;
use App\Repositories\MealRepository;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(FoodRepositoryInterface::class, FoodRepository::class);
        $this->app->bind(MealRepositoryInterface::class, MealRepository::class);
    }

    public function boot(): void
    {

    }

}
