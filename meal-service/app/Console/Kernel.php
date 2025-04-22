<?php

namespace App\Console;

use App\Console\Commands\MealSchedulerTracker;
use App\Console\Commands\RegisterServiceWithConsul;
use App\Models\Meal;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{

    // protected $commands = [
    //     RegisterServiceWithConsul::class,
    // ];
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('app:meal-scheduler-tracker')->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        // if (!$this->app->runningInConsole() || !in_array('package:discover', request()->server('argv', []))) {
        //     $this->commands[] = MealSchedulerTracker::class;
        // }
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
