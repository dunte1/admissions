<?php

namespace App\Console\Commands;

use App\Models\School;
use App\Models\Subscription;
use App\Notifications\Subscription\TrialEndingSoon;
use App\Notifications\Subscription\TrialExpired;
use App\Notifications\Subscription\SubscriptionExpiringSoon;
use App\Notifications\Subscription\SubscriptionExpired;
use App\Notifications\Subscription\GracePeriodEndingSoon;
use App\Notifications\Subscription\AccountSuspended;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessSubscriptionStatus extends Command
{
    protected $signature = 'subscriptions:process 
                            {--trial : Process trial expirations only}
                            {--expiring : Process expiring subscriptions only}
                            {--grace : Process grace period expirations only}
                            {--suspend : Process suspensions only}
                            {--notify : Send notifications only}
                            {--dry-run : Show what would happen without making changes}';

    protected $description = 'Process subscription statuses, handle expirations, grace periods, and suspensions';

    public function handle(): int
    {
        $this->info('Starting subscription processing...');

        $dryRun = $this->option('dry-run');
        $notifyOnly = $this->option('notify');

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        if (!$notifyOnly) {
            $this->processTrialExpirations($dryRun);
            $this->processExpiringSubscriptions($dryRun);
            $this->processGracePeriodExpirations($dryRun);
            $this->processSuspensions($dryRun);
        }

        $this->sendUpcomingExpiringNotifications();

        $this->info('Subscription processing complete!');
        Log::info('Subscription processing completed', ['dry_run' => $dryRun]);

        return Command::SUCCESS;
    }

    protected function processTrialExpirations(bool $dryRun): void
    {
        $this->info('Processing trial expirations...');

        $schools = School::where('subscription_status', 'trial')
            ->where('trial_ends_at', '<=', now())
            ->get();

        foreach ($schools as $school) {
            $this->line("  - Processing: {$school->name} (Trial expired)");

            if (!$dryRun) {
                $school->update([
                    'subscription_status' => 'expired',
                ]);

                $school->admins()->each(function ($admin) use ($school) {
                    $admin->notify(new TrialExpired($school));
                });

                $this->school->startGracePeriod();

                Log::info('Trial expired', ['school_id' => $school->id, 'school_name' => $school->name]);
            } else {
                $this->line("    [DRY RUN] Would expire trial and start grace period");
            }
        }
    }

    protected function processExpiringSubscriptions(bool $dryRun): void
    {
        $this->info('Processing subscription expirations...');

        $subscriptions = Subscription::where('status', 'active')
            ->where('expires_at', '<=', now())
            ->get();

        foreach ($subscriptions as $subscription) {
            $school = $subscription->school;
            $this->line("  - Processing: {$school->name} (Subscription expired)");

            if (!$dryRun) {
                $subscription->update(['status' => 'expired']);

                $school->update([
                    'subscription_status' => 'expired',
                ]);

                $graceDays = $subscription->plan?->grace_period_days ?? 3;
                $school->update(['grace_ends_at' => now()->addDays($graceDays)]);
                $school->update(['subscription_status' => 'grace_period']);

                $school->admins()->each(function ($admin) use ($school, $graceDays) {
                    $admin->notify(new SubscriptionExpired($school, $graceDays));
                });

                Log::info('Subscription expired', [
                    'school_id' => $school->id,
                    'subscription_id' => $subscription->id,
                ]);
            } else {
                $this->line("    [DRY RUN] Would expire subscription and start grace period");
            }
        }
    }

    protected function processGracePeriodExpirations(bool $dryRun): void
    {
        $this->info('Processing grace period expirations...');

        $schools = School::where('subscription_status', 'grace_period')
            ->where('grace_ends_at', '<=', now())
            ->get();

        foreach ($schools as $school) {
            $this->line("  - Processing: {$school->name} (Grace period expired)");

            if (!$dryRun) {
                $school->update([
                    'status' => 'suspended',
                    'subscription_status' => 'suspended',
                ]);

                $school->admins()->each(function ($admin) use ($school) {
                    $admin->notify(new AccountSuspended($school, 'Your subscription was not renewed after the grace period.'));
                });

                Log::info('School suspended due to grace period expiration', [
                    'school_id' => $school->id,
                    'school_name' => $school->name,
                ]);
            } else {
                $this->line("    [DRY RUN] Would suspend school");
            }
        }
    }

    protected function processSuspensions(): void
    {
        $this->info('Checking for manual suspension overrides...');

        $suspendedSchools = School::where('status', 'suspended')
            ->where('subscription_status', 'suspended')
            ->get();

        foreach ($suspendedSchools as $school) {
            $hasActiveSubscription = $school->subscriptions()
                ->where('status', 'active')
                ->where('expires_at', '>', now())
                ->exists();

            if ($hasActiveSubscription) {
                $this->line("  - Reactivating: {$school->name} (Has active subscription)");
                $school->update([
                    'status' => 'active',
                    'subscription_status' => 'active',
                    'grace_ends_at' => null,
                ]);

                Log::info('School reactivated with new subscription', [
                    'school_id' => $school->id,
                    'school_name' => $school->name,
                ]);
            }
        }
    }

    protected function sendUpcomingExpiringNotifications(): void
    {
        $this->info('Sending upcoming expiration notifications...');

        $this->sendTrialEndingSoonNotifications();
        $this->sendSubscriptionExpiringSoonNotifications();
        $this->sendGracePeriodEndingSoonNotifications();
    }

    protected function sendTrialEndingSoonNotifications(): void
    {
        $schools = School::where('subscription_status', 'trial')
            ->whereNotNull('trial_ends_at')
            ->whereBetween('trial_ends_at', [now(), now()->addDays(3)])
            ->get();

        foreach ($schools as $school) {
            $daysRemaining = (int) ceil(now()->diffInDays($school->trial_ends_at));

            $school->admins()->each(function ($admin) use ($school, $daysRemaining) {
                $admin->notify(new TrialEndingSoon($school, $daysRemaining));
            });

            $this->line("  - Sent trial ending notification to: {$school->name} ({$daysRemaining} days)");
        }
    }

    protected function sendSubscriptionExpiringSoonNotifications(): void
    {
        $subscriptions = Subscription::where('status', 'active')
            ->whereBetween('expires_at', [now(), now()->addDays(7)])
            ->get();

        foreach ($subscriptions as $subscription) {
            $school = $subscription->school;
            $daysRemaining = (int) ceil(now()->diffInDays($subscription->expires_at));

            $school->admins()->each(function ($admin) use ($school, $daysRemaining) {
                $admin->notify(new SubscriptionExpiringSoon($school, $daysRemaining));
            });

            $this->line("  - Sent expiring notification to: {$school->name} ({$daysRemaining} days)");
        }
    }

    protected function sendGracePeriodEndingSoonNotifications(): void
    {
        $schools = School::where('subscription_status', 'grace_period')
            ->whereNotNull('grace_ends_at')
            ->whereBetween('grace_ends_at', [now(), now()->addDays(1)])
            ->get();

        foreach ($schools as $school) {
            $daysRemaining = (int) ceil(now()->diffInDays($school->grace_ends_at));

            $school->admins()->each(function ($admin) use ($school, $daysRemaining) {
                $admin->notify(new GracePeriodEndingSoon($school, $daysRemaining));
            });

            $this->line("  - Sent urgent grace period notification to: {$school->name} ({$daysRemaining} days)");
        }
    }
}
