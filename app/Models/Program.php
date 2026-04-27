<?php

namespace App\Models;

use App\Traits\SchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    use HasFactory, SchoolScope;

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'short_name',
        'program_code',
        'description',
        'department_id',
        'duration_years',
        'level',
        'tuition_per_year',
        'capacity',
        'min_capacity',
        'requirements',
        'career_opportunities',
        'is_active',
        'min_mean_grade',
        'subject_requirements',
        'alternative_qualification',
        'duration_display',
        'certification_authority',
    ];

    protected $casts = [
        'tuition_per_year' => 'decimal:2',
        'capacity' => 'integer',
        'min_capacity' => 'integer',
        'is_active' => 'boolean',
        'subject_requirements' => 'array',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function getAvailableSlots(): int
    {
        $enrolled = $this->applications()->where('status', 'approved')->count();
        return max(0, ($this->capacity ?? 0) - $enrolled);
    }

    public function isFull(): bool
    {
        return $this->getAvailableSlots() <= 0;
    }

    public function getLevelLabelAttribute(): string
    {
        return match($this->level) {
            'certificate' => 'Certificate',
            'diploma' => 'Diploma',
            'degree' => 'Degree',
            'masters' => 'Masters',
            'phd' => 'PhD',
            default => ucfirst($this->level),
        };
    }

    public static function gradePoints(): array
    {
        return [
            'A' => 12, 'A-' => 11, 'B+' => 10, 'B' => 9, 'B-' => 8,
            'C+' => 7, 'C' => 6, 'C-' => 5, 'D+' => 4, 'D' => 3, 'D-' => 2, 'E' => 1,
        ];
    }

    public static function meanGradeFromPoints(float $points): string
    {
        if ($points >= 11.5) return 'A';
        if ($points >= 10.5) return 'A-';
        if ($points >= 9.5) return 'B+';
        if ($points >= 8.5) return 'B';
        if ($points >= 7.5) return 'B-';
        if ($points >= 6.5) return 'C+';
        if ($points >= 5.5) return 'C';
        if ($points >= 4.5) return 'C-';
        if ($points >= 3.5) return 'D+';
        if ($points >= 2.5) return 'D';
        return 'E';
    }

    public static function gradeToPoints(string $grade): int
    {
        return self::gradePoints()[strtoupper($grade)] ?? 0;
    }

    public static function gradeComparison(string $userGrade, string $requiredGrade): int
    {
        $userPoints = self::gradeToPoints($userGrade);
        $requiredPoints = self::gradeToPoints($requiredGrade);

        if ($userPoints === $requiredPoints) return 0;
        return $userPoints > $requiredPoints ? 1 : -1;
    }

    public function meetsMeanGradeRequirement(?string $userMeanGrade): bool
    {
        if (!$this->min_mean_grade || !$userMeanGrade) return true;
        return self::gradeComparison($userMeanGrade, $this->min_mean_grade) >= 0;
    }

    public function meetsSubjectRequirements(array $userSubjects): array
    {
        if (!$this->subject_requirements) return ['eligible' => true, 'missing' => []];

        $missing = [];
        foreach ($this->subject_requirements as $req) {
            $subjectKey = $req['subject'];
            $requiredGrade = $req['grade'];

            if (!isset($userSubjects[$subjectKey])) {
                $missing[] = $subjectKey;
                continue;
            }

            if (self::gradeComparison($userSubjects[$subjectKey], $requiredGrade) < 0) {
                $missing[] = $subjectKey;
            }
        }

        return ['eligible' => empty($missing), 'missing' => $missing];
    }

    public function getEligibilityForUser(?string $meanGrade, array $subjects): array
    {
        $meetsMean = $this->meetsMeanGradeRequirement($meanGrade);
        $subjectCheck = $this->meetsSubjectRequirements($subjects);
        
        $reasons = [];
        if (!$meetsMean) {
            $reasons[] = "Mean grade {$meanGrade} does not meet minimum requirement of {$this->min_mean_grade}";
        }
        if (!$subjectCheck['eligible']) {
            $reasons[] = "Missing required subjects: " . implode(', ', $subjectCheck['missing']);
        }

        return [
            'eligible' => $meetsMean && $subjectCheck['eligible'],
            'reasons' => $reasons,
            'meets_mean_grade' => $meetsMean,
            'meets_subjects' => $subjectCheck['eligible'],
        ];
    }

    public static function findAlternatives(?string $educationLevel, ?string $meanGrade, array $subjects, ?int $excludeId = null): array
    {
        $query = self::active()
            ->withoutGlobalScopes()
            ->where('level', $educationLevel);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $programs = $query->get();
        $eligible = [];
        $notEligible = [];

        foreach ($programs as $program) {
            $eligibility = $program->getEligibilityForUser($meanGrade, $subjects);
            
            $programData = [
                'id' => $program->id,
                'name' => $program->name,
                'level' => $program->level,
                'level_label' => $program->level_label,
                'eligibility' => $eligibility,
            ];

            if ($eligibility['eligible']) {
                $eligible[] = $programData;
            } else {
                $notEligible[] = $programData;
            }
        }

        return ['eligible' => $eligible, 'not_eligible' => $notEligible];
    }
}
