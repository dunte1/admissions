<?php

namespace App\Console\Commands;

use App\Models\School;
use App\Multitenancy\Services\SubscriptionPlanService;
use Illuminate\Console\Command;

class SyncTenantFeatures extends Command
{
    protected $signature = 'tenant:sync-features {school_id? : Specific school ID to sync}';
    protected $description = 'Sync subscription plan features to school features table';

    public function handle(): int
    {
        $schoolId = $this->argument('school_id');

        if ($schoolId) {
            $schools = School::where('id', $schoolId)->get();
        } else {
            $schools = School::all();
        }

        $count = 0;

        foreach ($schools as $school) {
            try {
                SubscriptionPlanService::syncPlanFeatures($school);
                $this->info("Synced features for school: {$school->name} (ID: {$school->id})");
                $count++;
            } catch (\Exception $e) {
                $this->error("Failed to sync features for school {$school->name}: {$e->getMessage()}");
            }
        }

        $this->info("Completed syncing features for {$count} schools.");

        return Command::SUCCESS;
    }
}