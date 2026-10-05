<?php

namespace App\Models;

use App\Traits\SchoolScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SchoolScope, SoftDeletes;

    protected $fillable = [
        'school_id',
        'application_id',
        'type',
        'original_name',
        'stored_path',
        'mime_type',
        'size',
        'status',
        'verified_by',
        'verified_at',
        'rejection_reason',
        'admin_notes',
    ];

    protected $casts = [
        'size' => 'integer',
        'verified_at' => 'datetime',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('status', 'verified');
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', 'rejected');
    }

    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function verify(int $userId, ?string $notes = null): void
    {
        $this->update([
            'status' => 'verified',
            'verified_by' => $userId,
            'verified_at' => now(),
            'admin_notes' => $notes,
        ]);
    }

    public function reject(int $userId, string $reason, ?string $notes = null): void
    {
        $this->update([
            'status' => 'rejected',
            'verified_by' => $userId,
            'verified_at' => now(),
            'rejection_reason' => $reason,
            'admin_notes' => $notes,
        ]);
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'verified' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            default => 'bg-yellow-100 text-yellow-800',
        };
    }

    public function getUrlAttribute(): string
    {
        return asset("storage/{$this->stored_path}");
    }

    public function getSizeFormattedAttribute(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public static function getTypes(): array
    {
        return [
            'id_document' => 'ID Document',
            'birth_certificate' => 'Birth Certificate',
            'certificate' => 'Academic Certificate',
            'transcript' => 'Academic Transcript',
            'photo' => 'Passport Photo',
            'kcse_certificate' => 'KCSE Certificate',
            'kcse_results' => 'KCSE Results Slip',
            'recommendation_letter' => 'Recommendation Letter',
            'medical_form' => 'Medical Form',
            'police_clearance' => 'Police Clearance Certificate',
        ];
    }
}
