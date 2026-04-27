<?php

namespace App\Multitenancy\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolDomain extends Model
{
    protected $table = 'school_domains';

    protected $fillable = [
        'school_id',
        'domain',
        'is_primary',
        'is_active',
        'ssl_enabled',
        'ssl_cert_path',
        'ssl_key_path',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
        'ssl_enabled' => 'boolean',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isPrimary(): bool
    {
        return $this->is_primary;
    }

    public function getFullDomainAttribute(): string
    {
        return $this->domain;
    }

    public static function findByDomain(string $domain): ?self
    {
        return self::where('domain', $domain)
            ->where('is_active', true)
            ->first();
    }

    public static function getActiveDomainsForSchool(int $schoolId): array
    {
        return self::where('school_id', $schoolId)
            ->where('is_active', true)
            ->pluck('domain')
            ->toArray();
    }
}