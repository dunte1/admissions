<?php

namespace App\Models;

use App\Traits\DataScopeTrait;
use App\Traits\SchoolScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use HasFactory, DataScopeTrait, SchoolScope, SoftDeletes;

    protected $fillable = [
        'user_id',
        'school_id',
        'student_id',
        'intake_id',
        'application_number',
        'program_id',
        'program_choice_2',
        'program_choice_3',
        'status',
        'current_step',
        'form_data',
        'total_score',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'submitted_at',
    ];

    protected $casts = [
        'form_data' => 'array',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function intake(): BelongsTo
    {
        return $this->belongsTo(Intake::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class)->withoutGlobalScopes();
    }

    public function getProgramNameAttribute(): ?string
    {
        if ($this->program) {
            return $this->program->name;
        }
        
        $formData = is_array($this->form_data) ? $this->form_data : [];
        if (isset($formData['program_choices']['first'])) {
            $program = Program::withoutGlobalScopes()->find($formData['program_choices']['first']);
            return $program?->name;
        }
        
        return null;
    }

    public function admissionLetter(): HasMany
    {
        return $this->hasMany(AdmissionLetter::class);
    }

    public function programChoice2(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_choice_2');
    }

    public function programChoice3(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_choice_3');
    }

    public function getAllProgramsAttribute(): array
    {
        $programs = [$this->program];
        if ($this->programChoice2) {
            $programs[] = $this->programChoice2;
        }
        if ($this->programChoice3) {
            $programs[] = $this->programChoice3;
        }
        return array_filter($programs);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ApplicationNote::class);
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public static function generateNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $lastApp = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->latest('id')
            ->first();
        $sequence = $lastApp ? ((int) substr($lastApp->application_number, -4)) + 1 : 1;
        return 'APP' . $year . $month . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'under_review' => 'bg-blue-100 text-blue-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'waitlisted' => 'bg-orange-100 text-orange-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function scopeAccessible(Builder $query, $user = null): Builder
    {
        $user = $user ?? auth()->user();
        
        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->hasPermissionTo('view_applications')) {
            return $query;
        }

        if ($user->hasRole('student')) {
            return $query->where('user_id', $user->id);
        }

        return $query->whereRaw('1 = 0');
    }

    public function scopeCanReview(Builder $query): Builder
    {
        return $query->whereIn('status', ['pending', 'under_review']);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeForSchool(Builder $query, int $schoolId): Builder
    {
        return $query->where('school_id', $schoolId);
    }

    public function getAllDocumentsVerifiedAttribute(): bool
    {
        if (!$this->relationLoaded('documents')) {
            $this->loadCount(['documents as verified_docs_count' => fn($q) => $q->where('status', 'verified')]);
            $this->loadCount(['documents as total_docs_count']);
            return $this->verified_docs_count === $this->total_docs_count && $this->total_docs_count > 0;
        }
        return $this->documents->every(fn($doc) => $doc->status === 'verified');
    }

    public function getPendingDocumentsAttribute(): bool
    {
        if (!$this->relationLoaded('documents')) {
            return $this->documents()->where('status', 'pending')->exists();
        }
        return $this->documents->contains(fn($doc) => $doc->status === 'pending');
    }

    public function getHasOfferAttribute(): bool
    {
        if ($this->relationLoaded('admissionLetter')) {
            return $this->admissionLetter->contains(fn($letter) => $letter->type === 'admission');
        }
        return $this->admissionLetter()->where('type', 'admission')->exists();
    }

    public function scopeForIntake($query, $intakeId)
    {
        return $query->where('intake_id', $intakeId);
    }
}
