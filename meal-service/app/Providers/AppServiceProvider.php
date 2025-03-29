<?php

namespace App\Providers;

use App\Repositories\FoodRepository;
use App\Repositories\Interfaces\FoodRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(FoodRepositoryInterface::class, FoodRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
