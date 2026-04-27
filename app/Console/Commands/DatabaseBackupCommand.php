<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;

class DatabaseBackupCommand extends Command
{
    protected $signature = 'backup:database';
    protected $description = 'Create a database-only backup';

    public function handle(BackupService $backupService): int
    {
        $this->info('Starting database backup...');

        try {
            $backup = $backupService->createDatabaseBackup();
            
            $this->info("Database backup completed successfully!");
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

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Database backup failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
