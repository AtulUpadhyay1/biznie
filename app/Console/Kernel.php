<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Sellers are quoted off their last saved price, so chase the stale ones
        // once a morning. Needs the host's cron to run `schedule:run` every minute.
        $schedule->command('biznie:remind-price-update')
            ->dailyAt('09:30')
            ->timezone('Asia/Kolkata')
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
