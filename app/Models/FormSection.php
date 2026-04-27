<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'slug',
        'icon',
        'order',
        'is_active',
        'is_required',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_required' => 'boolean',
        'order' => 'integer',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(FormField::class)->orderBy('order');
    }

    public function activeFields(): HasMany
    {
        return $this->hasMany(FormField::class)->where('is_active', true)->orderBy('order');
    }

    public function scopeForSchool($query, ?int $schoolId = null)
    {
        return $query->where(function ($q) use ($schoolId) {
            $q->whereNull('school_id');
            if ($schoolId) {
                $q->orWhere('school_id', $schoolId);
            }
        });
    }

    public function scopeForSchoolSlug($query, ?int $schoolId, string $step)
    {
        // Map common steps to expected slugs
        $slugMap = [
            'personal' => ['personal', 'mutomo-personal'],
            'academic' => ['academic', 'mutomo-academic'],
            'guardian' => ['guardian', 'mutomo-guardian'],
            'documents' => ['documents', 'mutomo-documents'],
            'financial' => ['financial', 'mutomo-finance', 'finance'],
            'declaration' => ['declaration', 'mutomo-declaration'],
        ];

        $slugs = $slugMap[$step] ?? [$step];

        return $query->where(function ($q) use ($schoolId, $slugs) {
            // First try school-specific
            if ($schoolId) {
                $q->where('school_id', $schoolId)->whereIn('slug', $slugs);
                $q->orWhere(function ($q2) use ($slugs) {
                    $q2->whereNull('school_id')->whereIn('slug', $slugs);
                });
            } else {
                $q->whereNull('school_id')->whereIn('slug', $slugs);
            }
        })->orderByDesc('school_id')->limit(1);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
