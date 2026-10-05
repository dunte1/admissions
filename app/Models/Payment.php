<?php

namespace App\Models;

use App\Traits\SchoolScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SchoolScope, SoftDeletes;

    protected $fillable = [
        'school_id',
        'application_id',
        'user_id',
        'amount',
        'payment_type',
        'payment_method',
        'transaction_id',
        'phone_number',
        'status',
        'paid_at',
        'failure_reason',
        'mpesa_receipt',
        'paypal_transaction_id',
        'idempotency_key',
        'initiated_at',
        'completed_at',
        'callback_received_at',
        'ip_address',
        'user_agent',
        'receipt_number',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'initiated_at' => 'datetime',
        'completed_at' => 'datetime',
        'callback_received_at' => 'datetime',
    ];

    protected $hidden = [
        'idempotency_key',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(PaymentLog::class);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', 'failed');
    }

    public function scopeForMethod(Builder $query, string $method): Builder
    {
        return $query->where('payment_method', $method);
    }

    public function scopePaidBetween(Builder $query, $start, $end): Builder
    {
        return $query->whereBetween('paid_at', [$start, $end]);
    }

    public function scopeByIdempotencyKey(Builder $query, string $key): Builder
    {
        return $query->where('idempotency_key', $key);
    }

    public function scopeAdmissionFee(Builder $query): Builder
    {
        return $query->where('payment_type', 'admission_fee');
    }

    public function scopeCommitmentFee(Builder $query): Builder
    {
        return $query->where('payment_type', 'commitment_fee');
    }

    public function scopeManualPending(Builder $query): Builder
    {
        return $query->where('status', 'manual_pending');
    }

    public function getPaymentTypeLabelAttribute(): string
    {
        return match($this->payment_type) {
            'admission_fee' => 'Admission Fee',
            'commitment_fee' => 'Commitment Fee',
            default => 'Payment',
        };
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isDuplicate(string $idempotencyKey): bool
    {
        return self::where('idempotency_key', $idempotencyKey)
            ->whereIn('status', ['pending', 'completed'])
            ->exists();
    }

    public function markAsCompleted(?string $receipt = null): void
    {
        $this->update([
            'status' => 'completed',
            'paid_at' => now(),
            'completed_at' => now(),
            'mpesa_receipt' => $receipt,
        ]);
        
        if (!$this->receipt_number) {
            $this->generateReceiptNumber();
        }
        
        $this->logEvent('completed', 'Payment confirmed successful');
    }

    public function markAsFailed(string $reason): void
    {
        $this->update([
            'status' => 'failed',
            'failure_reason' => $reason,
        ]);
        
        $this->logEvent('failed', 'Payment failed: ' . $reason);
    }

    public function logEvent(string $event, string $description, array $metadata = []): void
    {
        PaymentLog::create([
            'payment_id' => $this->id,
            'school_id' => $this->school_id,
            'user_id' => $this->user_id,
            'event' => $event,
            'description' => $description,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'manual_pending' => 'bg-yellow-100 text-yellow-800',
            'completed' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
            'rejected' => 'bg-red-100 text-red-800',
            'refunded' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public static function generateIdempotencyKey(string $applicationId, string $phone, string $method): string
    {
        $minute = now()->format('Y-m-d-Hi');
        return hash('sha256', $applicationId . $phone . $method . $minute);
    }

    public function generateReceiptNumber(): string
    {
        $year = now()->format('Y');
        $lastPayment = self::whereYear('created_at', $year)
            ->whereNotNull('receipt_number')
            ->orderBy('id', 'desc')
            ->first();
        
        $nextNumber = $lastPayment ? (intval(substr($lastPayment->receipt_number, -5)) + 1) : 1;
        $receiptNumber = 'RCP-' . $year . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        
        $this->update(['receipt_number' => $receiptNumber]);
        
        return $receiptNumber;
    }

    public function getFormattedAmountAttribute(): string
    {
        $school = $this->school;
        $symbol = $school?->currency_symbol ?? config('app-config.currency_symbol', 'KSh');
        return $symbol . ' ' . number_format($this->amount, 2);
    }

    public function getSchoolBranding(): array
    {
        $school = $this->school;
        return [
            'name' => $school?->name ?? system_setting('system_name', config('app.name')),
            'logo' => $school?->logoUrl ?? asset('images/default-school.png'),
            'email' => $school?->email ?? system_setting('system_email', config('mail.mailers.smtp.username')),
            'phone' => $school?->phone ?? system_setting('system_phone'),
            'address' => $school?->address ?? system_setting('system_address'),
            'primary_color' => $school?->primaryColor ?? system_setting('primary_color', '#7C3AED'),
            'tagline' => $school?->getConfig('tagline') ?? system_setting('tagline', 'Excellence in Education'),
        ];
    }
}
