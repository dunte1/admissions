<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;

class FullBackupCommand extends Command
{
    protected $signature = 'backup:full {--keep}';
    protected $description = 'Create a full system backup';

    public function handle(BackupService $backupService): int
    {
        $this->info('Starting full system backup...');

        try {
            $backup = $backupService->createFullBackup();
            
            $this->info("Backup completed successfully!");
            $this->table(
                ['ID', 'Type', 'File', 'Size', 'Status', 'Created'],
                [[
                    $backup->id,
                    $backup->type,
                    $backup->file_name,
                    $backup->formatted_size,
                    $backup->status,
                    $backup->created_at->format('Y-m-d H:i:s'),
                ]]
            );

            if ($this->option('keep')) {
                $this->info('Old backups cleanup skipped (--keep flag set)');
            } else {
                $deleted = $backupService->cleanupOldBackups();
                $this->info("Cleaned up {$deleted} old backups");
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Backup failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
