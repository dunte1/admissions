<?php

namespace App\Multitenancy;

use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TenantManager
{
    protected static ?int $currentSchoolId = null;
    protected static ?School $currentSchool = null;
    protected static bool $tenantResolved = false;

    public static function resolve(Request $request): ?School
    {
        if (self::$tenantResolved && self::$currentSchool) {
            return self::$currentSchool;
        }

        $school = null;

        if ($request->has('school_id')) {
            $school = School::where('id', $request->get('school_id'))
                ->where('status', 'active')
                ->first();
        }

        if (!$school && $request->has('school')) {
            $school = School::where('slug', $request->get('school'))
                ->orWhere('code', $request->get('school'))
                ->where('status', 'active')
                ->first();
        }

        if (!$school) {
            $host = $request->getHost();
            $school = self::resolveFromDomain($host);
        }

        if ($school) {
            self::setCurrentSchool($school);
            self::$tenantResolved = true;
        }

        return $school;
    }

    public static function resolveFromDomain(string $host): ?School
    {
        $baseDomain = config('app.base_domain');
        
        if ($baseDomain && str_ends_with($host, '.' . $baseDomain)) {
            $subdomain = str_replace('.' . $baseDomain, '', $host);
            
            if ($subdomain !== config('app.domain') && $subdomain !== 'www') {
                return School::where('subdomain', $subdomain)
                    ->where('status', 'active')
                    ->first();
            }
        }

        $customDomainSchool = Cache::remember("domain:{$host}", 3600, function () use ($host) {
            return DB::table('school_domains')
                ->where('domain', $host)
                ->where('is_active', true)
                ->first();
        });

        if ($customDomainSchool) {
            return School::find($customDomainSchool->school_id);
        }

        return School::where('domain', $host)
            ->where('status', 'active')
            ->first();
    }

    public static function setCurrentSchool(?School $school): void
    {
        self::$currentSchool = $school;
        self::$currentSchoolId = $school?->id;
        
        if ($school) {
            session(['school_id' => $school->id]);
            app()->instance('current_school', $school);
            School::setCurrentId($school->id);
        }
    }

    public static function getCurrentSchool(): ?School
    {
        if (self::$currentSchool) {
            return self::$currentSchool;
        }

        $schoolId = self::getSchoolId();
        
        if ($schoolId) {
            self::$currentSchool = School::find($schoolId);
            return self::$currentSchool;
        }

        return null;
    }

    public static function getSchoolId(): ?int
    {
        if (self::$currentSchoolId !== null) {
            return self::$currentSchoolId;
        }

        if (auth()->check() && !auth()->user()->hasRole('super_admin')) {
            return auth()->user()->school_id;
        }

        return session('school_id');
    }

    public static function getCurrentTenant(): ?School
    {
        return self::getCurrentSchool();
    }

    public static function isTenantResolved(): bool
    {
        return self::$tenantResolved;
    }

    public static function forgetTenant(): void
    {
        self::$currentSchool = null;
        self::$currentSchoolId = null;
        self::$tenantResolved = false;
        session()->forget('school_id');
    }

    public static function checkFeature(string $feature, string $requiredStatus = 'active'): bool
    {
        $school = self::getCurrentSchool();
        
        if (!$school) {
            return false;
        }

        $feature = $school->features()->where('feature', $feature)->first();
        
        if (!$feature) {
            return $requiredStatus === 'disabled';
        }

        return $feature->status === $requiredStatus;
    }

    public static function isFeatureActive(string $feature): bool
    {
        return self::checkFeature($feature, 'active');
    }

    public static function isFeaturePaused(string $feature): bool
    {
        return self::checkFeature($feature, 'paused');
    }

    public static function isFeatureDisabled(string $feature): bool
    {
        $school = self::getCurrentSchool();
        
        if (!$school) {
            return true;
        }

        $feature = $school->features()->where('feature', $feature)->first();
        
        return !$feature || $feature->status === 'disabled';
    }
}