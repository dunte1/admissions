<?php

namespace App\Models;

use App\Traits\SchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdmissionLetter extends Model
{
    use HasFactory, SchoolScope;

    protected $fillable = [
        'school_id',
        'application_id',
        'letter_number',
        'type',
        'status',
        'issue_date',
        'response_deadline',
        'responded_at',
        'additional_conditions',
        'remarks',
        'pdf_path',
        'interview_date',
        'interview_venue',
        'interview_instructions',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'response_deadline' => 'date',
        'responded_at' => 'datetime',
        'interview_date' => 'datetime',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public static function generateLetterNumber(): string
    {
        $prefix = 'LET';
        $year = date('Y');
        
        $latest = self::where('letter_number', 'like', "{$prefix}{$year}%")
            ->orderBy('letter_number', 'desc')
            ->first();
        
        if ($latest) {
            $sequence = (int) substr($latest->letter_number, -4) + 1;
        } else {
            $sequence = 1;
        }
        
        return $prefix . $year . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isDeclined(): bool
    {
        return $this->status === 'declined';
    }

    public function isPending(): bool
    {
        return in_array($this->status, ['sent', 'acknowledged']);
    }

    public function isExpired(): bool
    {
        return $this->response_deadline && now()->gt($this->response_deadline) && $this->isPending();
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['draft', 'sent', 'acknowledged']);
    }

    public function scopeAwaitingResponse($query)
    {
        return $query->where('status', 'sent')
            ->where(function ($q) {
                $q->whereNull('response_deadline')
                    ->orWhere('response_deadline', '>=', now()->toDateString());
            });
    }
}