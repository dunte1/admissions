<?php

namespace App\Models;

use App\Traits\SchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentLog extends Model
{
    use HasFactory, SchoolScope;

    public $timestamps = false;

    protected $fillable = [
        'payment_id',
        'school_id',
        'user_id',
        'event',
        'description',
        'metadata',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public static function log(
        Payment $payment,
        string $event,
        string $description,
        ?array $metadata = null,
        ?int $userId = null
    ): self {
        return self::create([
            'payment_id' => $payment->id,
            'school_id' => $payment->school_id,
            'user_id' => $userId ?? $payment->user_id,
            'event' => $event,
            'description' => $description,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }

    public static function logInitiated(Payment $payment): self
    {
        return self::log($payment, 'initiated', 'Payment initiated', [
            'amount' => $payment->amount,
            'method' => $payment->payment_method,
            'phone' => $payment->phone_number,
        ]);
    }

    public static function logCallback(Payment $payment, array $callbackData): self
    {
        return self::log($payment, 'callback_received', 'Payment callback received from gateway', [
            'callback_data' => $callbackData,
        ]);
    }

    public static function logCompleted(Payment $payment, ?string $receipt = null): self
    {
        return self::log($payment, 'completed', 'Payment completed successfully', [
            'receipt' => $receipt,
            'completed_at' => now()->toIso8601String(),
        ]);
    }

    public static function logFailed(Payment $payment, string $reason): self
    {
        return self::log($payment, 'failed', 'Payment failed: ' . $reason, [
            'reason' => $reason,
        ]);
    }

    public static function logVerified(Payment $payment): self
    {
        return self::log($payment, 'verified', 'Payment status verified with gateway');
    }

    public static function logTimeout(Payment $payment): self
    {
        return self::log($payment, 'timeout', 'Payment request timed out');
    }

    public static function logDuplicateAttempt(Payment $payment, string $existingTransactionId): self
    {
        return self::log($payment, 'duplicate_attempt', 'Duplicate payment attempt detected', [
            'existing_transaction_id' => $existingTransactionId,
        ]);
    }

    public function scopeByEvent(Builder $query, string $event): Builder
    {
        return $query->where('event', $event);
    }

    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
