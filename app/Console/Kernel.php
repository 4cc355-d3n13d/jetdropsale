<?php

namespace App\Console;

use App\Console\Commands\GarbageCollector;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Class Kernel
 */
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     */
    protected $commands = [];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(Commands\BillingSchedule::class)->daily();
        //$schedule->command(Commands\FeedBot::class)->cron('*/' . config('settings.admitad.ali.parse.update_interval', 5) . ' * * * *');
        $schedule->command(GarbageCollector::class)->cron('*/' . GarbageCollector::INTERVAL_MIN . ' * * * *');
        $schedule->command('horizon:snapshot')->everyFiveMinutes();
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
