<?php

namespace App\Multitenancy;

use App\Models\School;
use Illuminate\Database\Eloquent\Builder;

trait TenantScope
{
    public static function scopeTenant(Builder $query, ?int $schoolId = null): Builder
    {
        $schoolId = $schoolId ?? TenantManager::getSchoolId();
        
        if ($schoolId === null) {
            return $query;
        }
        
        return $query->where('school_id', $schoolId);
    }

    public static function current(?School $school = null): ?School
    {
        if ($school !== null) {
            TenantManager::setCurrentSchool($school);
            return $school;
        }
        
        return TenantManager::getCurrentSchool();
    }

    public static function getTenantId(): ?int
    {
        return TenantManager::getSchoolId();
    }
}