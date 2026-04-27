<?php

namespace App\Traits;

use App\Models\School;
use App\Scopes\SchoolScope as SchoolScopeScope;
use Illuminate\Database\Eloquent\Builder;

trait SchoolScope
{
    protected static function bootSchoolScope(): void
    {
        static::addGlobalScope(new SchoolScopeScope);
    }

    public function schoolRelation()
    {
        return $this->belongsTo(School::class);
    }

    public function scopeForSchool(Builder $query, ?int $schoolId = null): Builder
    {
        $schoolId = $schoolId ?? School::getCurrentId();

        if (!$schoolId) {
            return $query;
        }

        return $query->where('school_id', $schoolId);
    }

    public function scopeForSchoolExplicit(Builder $query, int $schoolId): Builder
    {
        return $query->where('school_id', $schoolId);
    }

    public function scopeAllSchools(Builder $query): Builder
    {
        return $query->withoutGlobalScope(SchoolScopeScope::class);
    }

    public function scopeWithSchool(Builder $query): Builder
    {
        return $query->withoutGlobalScope(SchoolScopeScope::class)->with('school');
    }

    public function getSchoolIdAttribute(): ?int
    {
        if (isset($this->attributes['school_id'])) {
            return $this->attributes['school_id'];
        }
        return null;
    }
}