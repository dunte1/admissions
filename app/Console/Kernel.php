<?php

namespace App\Console;

use App\Jobs\SendBroadcastJob;
use App\Models\Broadcast;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            $scheduledBroadcasts = Broadcast::scheduled()
                ->where('scheduled_at', '<=', now())
                ->where('status', 'scheduled')
                ->get();

            foreach ($scheduledBroadcasts as $broadcast) {
                SendBroadcastJob::dispatch($broadcast);
            }
        })->everyMinute()->name('process-scheduled-broadcasts');

        $schedule->command('subscriptions:process')
            ->daily()
            ->at('00:00')
            ->withoutOverlapping()
            ->runInBackground()
            ->name('process-subscriptions');

        $schedule->command('backup:full')
            ->daily()
            ->at('02:00')
            ->withoutOverlapping()
            ->runInBackground()
            ->name('backup-full');

        $schedule->command('backup:database')
            ->daily()
            ->at('03:00')
            ->withoutOverlapping()
            ->runInBackground()
            ->name('backup-database');

        $schedule->command('backup:schools')
            ->daily()
            ->at('03:30')
            ->withoutOverlapping()
            ->runInBackground()
            ->name('backup-schools');

        $schedule->command('backup:cleanup')
            ->daily()
            ->at('04:00')
            ->withoutOverlapping()
            ->name('backup-cleanup');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}
