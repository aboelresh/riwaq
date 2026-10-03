<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Clean up expired password reset codes — runs daily at midnight
        $schedule->command('app:cleanup-expired-codes')
                 ->daily()
                 ->withoutOverlapping()
                 ->runInBackground();

        // Clear old AI usage logs (keep last 3 months) — runs monthly
        $schedule->command('app:cleanup-ai-logs')
                 ->monthly()
                 ->withoutOverlapping();

        // Health check — log to audit if any system down
        $schedule->command('app:health-check-log')
                 ->everyFiveMinutes()
                 ->withoutOverlapping();
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}