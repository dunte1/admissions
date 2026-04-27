<?php

namespace App\Console\Commands;

use App\Models\Backup;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackupCleanupCommand extends Command
{
    protected $signature = 'backup:cleanup {--days=}';
    protected $description = 'Clean up old backups';

    public function handle(): int
    {
        $days = $this->option('days') ?? 30;
        
        $this->info("Cleaning up backups older than {$days} days...");

        $backups = Backup::where('created_at', '<', now()->subDays($days))
            ->where('status', Backup::STATUS_COMPLETED)
            ->get();

        if ($backups->isEmpty()) {
            $this->info('No old backups to clean up');
            return Command::SUCCESS;
        }

        $totalSize = 0;
        $deletedCount = 0;

        foreach ($backups as $backup) {
            if ($backup->file_path && Storage::disk('local')->exists($backup->file_path)) {
                $totalSize += Storage::disk('local')->size($backup->file_path);
                Storage::disk('local')->delete($backup->file_path);
            }
            $backup->delete();
            $deletedCount++;
        }

        $this->info("Deleted {$deletedCount} backup(s), freed " . number_format($totalSize / 1048576, 2) . " MB");

        return Command::SUCCESS;
    }
}
