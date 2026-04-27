<?php

namespace App\Console\Commands;

use App\Models\School;
use App\Services\BackupService;
use Illuminate\Console\Command;

class SchoolBackupCommand extends Command
{
    protected $signature = 'backup:schools {--school_id=}';
    protected $description = 'Create backups for all schools or a specific school';

    public function handle(BackupService $backupService): int
    {
        $schoolId = $this->option('school_id');
        
        if ($schoolId) {
            $school = School::find($schoolId);
            if (!$school) {
                $this->error("School with ID {$schoolId} not found");
                return Command::FAILURE;
            }
            $schools = collect([$school]);
        } else {
            $schools = School::active()->get();
        }

        $this->info("Starting school backup(s) for {$schools->count()} school(s)...");

        $bar = $this->output->createProgressBar($schools->count());
        $bar->start();

        $successCount = 0;
        $failCount = 0;

        foreach ($schools as $school) {
            try {
                $backupService->createSchoolBackup($school);
                $successCount++;
            } catch (\Exception $e) {
                $failCount++;
                $this->newLine();
                $this->error("  Failed for {$school->code}: " . $e->getMessage());
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        
        $this->info("School backup completed: {$successCount} successful, {$failCount} failed");

        return $failCount > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
