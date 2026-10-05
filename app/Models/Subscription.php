<?php

namespace App\Models;

use App\Traits\SchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Subscription extends Model
{
    use HasFactory, SchoolScope;

    protected $fillable = [
        'school_id',
        'plan_id',
        'status',
        'starts_at',
        'expires_at',
        'cancelled_at',
        'billing_cycles',
        'amount_paid',
        'payment_method',
        'transaction_id',
        'notes',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'amount_paid' => 'decimal:2',
    ];

    public const STATUS_ACTIVE = 'active';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_PENDING = 'pending';
    public const STATUS_TRIAL = 'trial';

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE && $this->expires_at->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function daysRemaining(): int
    {
        if ($this->isExpired()) {
            return 0;
        }
        return now()->diffInDays($this->expires_at);
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'bg-green-100 text-green-800',
            self::STATUS_EXPIRED => 'bg-red-100 text-red-800',
            self::STATUS_CANCELLED => 'bg-gray-100 text-gray-800',
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            self::STATUS_TRIAL => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('status', self::STATUS_EXPIRED)
              ->orWhere(function ($sq) {
                  $sq->where('status', self::STATUS_ACTIVE)
                     ->where('expires_at', '<', now());
              });
        });
    }

    public function scopeExpiringSoon($query, int $days = 7)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->whereBetween('expires_at', [now(), now()->addDays($days)]);
    }

    public function cancel(): bool
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancelled_at' => now(),
        ]);
        return true;
    }

    public function renew(?int $days = null): bool
    {
        $days = $days ?? $this->plan->duration_days;
        $newExpiry = $this->expires_at->isFuture() 
            ? $this->expires_at->addDays($days)
            : now()->addDays($days);
            
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'expires_at' => $newExpiry,
            'cancelled_at' => null,
        ]);
        return true;
    }

    protected function formattedExpiry(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->expires_at->format('M d, Y')
        );
    }
}
