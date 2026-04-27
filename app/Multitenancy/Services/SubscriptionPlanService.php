<?php

namespace App\Multitenancy\Services;

use App\Models\Plan;
use App\Models\School;
use App\Models\Subscription;
use App\Models\SchoolFeature;
use Illuminate\Support\Collection;

class SubscriptionPlanService
{
    protected static array $defaultFeatures = [
        'admissions' => true,
        'messaging' => true,
        'notifications' => true,
    ];

    public static function getPlanFeatures(Plan $plan): array
    {
        $features = $plan->features ?? [];
        
        if (is_string($features)) {
            $features = json_decode($features, true) ?? [];
        }

        return array_merge(self::$defaultFeatures, $features);
    }

    public static function getSchoolFeatures(School $school): Collection
    {
        return SchoolFeature::where('school_id', $school->id)->get();
    }

    public static function isFeatureAvailableInPlan(Plan $plan, string $feature): bool
    {
        $features = self::getPlanFeatures($plan);
        
        return isset($features[$feature]) && $features[$feature] === true;
    }

    public static function canAccessFeature(School $school, string $feature): bool
    {
        if (!$school->isActive()) {
            return false;
        }

        if (!$school->hasActiveSubscription()) {
            return false;
        }

        $subscription = $school->activeSubscription;
        
        if (!$subscription) {
            return false;
        }

        $plan = $subscription->plan;
        
        if (!$plan) {
            return true;
        }

        if (!self::isFeatureAvailableInPlan($plan, $feature)) {
            return false;
        }

        $schoolFeature = SchoolFeature::where('school_id', $school->id)
            ->where('feature', $feature)
            ->first();

        if (!$schoolFeature) {
            return true;
        }

        return $schoolFeature->isActive();
    }

    public static function enableFeature(School $school, string $feature): SchoolFeature
    {
        $subscription = $school->activeSubscription;
        
        if (!$subscription || !self::isFeatureAvailableInPlan($subscription->plan, $feature)) {
            throw new \Exception("Feature '{$feature}' is not available in the school's subscription plan.");
        }

        return SchoolFeature::updateOrCreate(
            [
                'school_id' => $school->id,
                'feature' => $feature,
            ],
            [
                'status' => SchoolFeature::STATUS_ACTIVE,
                'activated_at' => now(),
                'deactivated_at' => null,
                'paused_at' => null,
            ]
        );
    }

    public static function pauseFeature(School $school, string $feature): SchoolFeature
    {
        $schoolFeature = SchoolFeature::where('school_id', $school->id)
            ->where('feature', $feature)
            ->first();

        if (!$schoolFeature || !$schoolFeature->isActive()) {
            throw new \Exception("Feature '{$feature}' is not active.");
        }

        $schoolFeature->pause();

        return $schoolFeature;
    }

    public static function disableFeature(School $school, string $feature): SchoolFeature
    {
        return SchoolFeature::updateOrCreate(
            [
                'school_id' => $school->id,
                'feature' => $feature,
            ],
            [
                'status' => SchoolFeature::STATUS_DISABLED,
                'deactivated_at' => now(),
                'paused_at' => null,
            ]
        );
    }

    public static function resumeFeature(School $school, string $feature): SchoolFeature
    {
        $schoolFeature = SchoolFeature::where('school_id', $school->id)
            ->where('feature', $feature)
            ->first();

        if (!$schoolFeature || !$schoolFeature->isPaused()) {
            throw new \Exception("Feature '{$feature}' is not paused.");
        }

        $schoolFeature->resume();

        return $schoolFeature;
    }

    public static function getAvailableFeatures(School $school): array
    {
        $subscription = $school->activeSubscription;
        
        if (!$subscription) {
            return [];
        }

        $plan = $subscription->plan;
        
        if (!$plan) {
            return array_keys(self::$defaultFeatures);
        }

        $planFeatures = self::getPlanFeatures($plan);
        
        return array_keys(array_filter($planFeatures, fn($enabled) => $enabled));
    }

    public static function getFeatureStatus(School $school, string $feature): ?string
    {
        $schoolFeature = SchoolFeature::where('school_id', $school->id)
            ->where('feature', $feature)
            ->first();

        return $schoolFeature?->status;
    }

    public static function syncPlanFeatures(School $school): void
    {
        $subscription = $school->activeSubscription;
        
        if (!$subscription) {
            return;
        }

        $plan = $subscription->plan;
        
        if (!$plan) {
            return;
        }

        $planFeatures = self::getPlanFeatures($plan);
        $allFeatures = SchoolFeature::FEATURES;

        foreach ($allFeatures as $feature => $name) {
            $enabledInPlan = isset($planFeatures[$feature]) && $planFeatures[$feature];
            
            $existingFeature = SchoolFeature::where('school_id', $school->id)
                ->where('feature', $feature)
                ->first();

            if ($enabledInPlan && !$existingFeature) {
                SchoolFeature::create([
                    'school_id' => $school->id,
                    'feature' => $feature,
                    'status' => SchoolFeature::STATUS_ACTIVE,
                    'activated_at' => now(),
                ]);
            } elseif (!$enabledInPlan && $existingFeature) {
                $existingFeature->update([
                    'status' => SchoolFeature::STATUS_DISABLED,
                    'deactivated_at' => now(),
                ]);
            }
        }
    }
}