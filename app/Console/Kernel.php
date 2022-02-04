<?php

namespace App\Console;

use App\Http\Controllers\AccrualAniController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ParsePackController;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /**
         * TODO: Не забудь код допилить для команд
         */
        $schedule->command('inventory:update')->hourlyAt(50);
        $schedule->command('ani:accrual')->hourlyAt(00);
        //$schedule->command('pool:refill')->everyFiveMinutes();
        $schedule->command('parse:pack')->everyFiveMinutes();
        //$schedule->command('wax:lock')->dailyAt('00:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
