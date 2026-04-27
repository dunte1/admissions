<?php

namespace App\Console\Commands;

use App\Models\School;
use App\Models\Subscription;
use Illuminate\Console\Command;

class CheckSubscriptionStatus extends Command
{
    protected $signature = 'app:check-subscription {school_id?}';

    protected $description = 'Check subscription status for a school';

    public function handle()
    {
        $schoolId = $this->argument('school_id');
        
        if ($schoolId) {
            $school = School::find($schoolId);
            if (!$school) {
                $this->error("School with ID {$schoolId} not found");
                return 1;
            }
        } else {
            $school = School::first();
        }
        
        $this->info("School: {$school->name} (ID: {$school->id})");
        $this->info("School Status: {$school->status}");
        $this->info("Subscription Status: {$school->subscription_status}");
        
        $subscription = $school->activeSubscription;
        if ($subscription) {
            $this->info("Active Subscription Found:");
            $this->info("  - Plan: " . ($subscription->plan ? $subscription->plan->name : 'N/A'));
            $this->info("  - Status: {$subscription->status}");
            $this->info("  - Expires: {$subscription->expires_at}");
            $this->info("  - Days Remaining: " . $subscription->daysRemaining());
        } else {
            $this->warn("No active subscription found!");
            
            if ($this->confirm('Would you like to create a trial subscription?')) {
                $this->call('app:create-trial-subscription', ['school_id' => $school->id]);
            }
        }
        
        return 0;
    }
}
