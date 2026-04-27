<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class BroadcastTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'school_id',
        'name',
        'subject',
        'content',
        'type',
        'category',
        'variables',
        'is_active',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForSchool(Builder $query, ?int $schoolId): Builder
    {
        if ($schoolId) {
            return $query->where('school_id', $schoolId);
        }
        return $query->whereNull('school_id');
    }

    public function scopeGlobal(Builder $query): Builder
    {
        return $query->whereNull('school_id');
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeInCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    public function getAvailableVariablesAttribute(): array
    {
        return $this->variables ?? [
            '{{name}}',
            '{{first_name}}',
            '{{last_name}}',
            '{{email}}',
            '{{application_number}}',
            '{{school_name}}',
            '{{program_name}}',
            '{{status}}',
            '{{date}}',
            '{{year}}',
        ];
    }
}
