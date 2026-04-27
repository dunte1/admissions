<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Models\School;

class Broadcast extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'school_id',
        'title',
        'message',
        'type',
        'targeting',
        'status',
        'scheduled_at',
        'sent_at',
        'total_recipients',
        'sent_count',
        'delivered_count',
        'failed_count',
        'is_test',
    ];

    protected $casts = [
        'targeting' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(BroadcastRecipient::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(BroadcastLog::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(BroadcastAttachment::class);
    }

    public function scopeForSchool(Builder $query, ?int $schoolId = null): Builder
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

    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', 'scheduled')
            ->where('scheduled_at', '<=', now());
    }

    public static function parseVariables(string $content, array $data = []): string
    {
        $variables = [
            '{{name}}' => $data['name'] ?? 'User',
            '{{first_name}}' => $data['first_name'] ?? '',
            '{{last_name}}' => $data['last_name'] ?? '',
            '{{email}}' => $data['email'] ?? '',
            '{{application_number}}' => $data['application_number'] ?? '',
            '{{school_name}}' => $data['school_name'] ?? '',
            '{{program_name}}' => $data['program_name'] ?? '',
            '{{status}}' => $data['status'] ?? '',
            '{{date}}' => now()->format('M d, Y'),
            '{{year}}' => now()->format('Y'),
        ];

        return str_replace(array_keys($variables), array_values($variables), $content);
    }

    public function getTargetingSummaryAttribute(): string
    {
        $targeting = $this->targeting ?? [];
        $parts = [];

        if (!empty($targeting['schools']) && $targeting['schools'] === 'all') {
            $parts[] = 'All Schools';
        } elseif (!empty($targeting['schools'])) {
            $parts[] = count($targeting['schools']) . ' School(s)';
        }

        if (!empty($targeting['roles'])) {
            $parts[] = implode(', ', array_map('ucfirst', $targeting['roles']));
        }

        if (!empty($targeting['application_status'])) {
            $parts[] = 'Status: ' . implode(', ', $targeting['application_status']);
        }

        return empty($parts) ? 'All Users' : implode(' | ', $parts);
    }

    public function getDeliveryRateAttribute(): float
    {
        if ($this->sent_count === 0) return 0;
        return round(($this->delivered_count / $this->sent_count) * 100, 1);
    }

    public function markAsSending(): void
    {
        $this->update(['status' => 'sending']);
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }
}
