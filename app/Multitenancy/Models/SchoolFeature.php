<?php

namespace App\Multitenancy\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolFeature extends Model
{
    protected $table = 'school_features';

    protected $fillable = [
        'school_id',
        'feature',
        'status',
        'metadata',
        'activated_at',
        'deactivated_at',
        'paused_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'activated_at' => 'datetime',
        'deactivated_at' => 'datetime',
        'paused_at' => 'datetime',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_PAUSED = 'paused';
    const STATUS_DISABLED = 'disabled';

    const FEATURE_ADMISSIONS = 'admissions';
    const FEATURE_ONLINE_EXAMS = 'online_exams';
    const FEATURE_FINANCE = 'finance';
    const FEATURE_LIBRARY = 'library';
    const FEATURE_NOTIFICATIONS = 'notifications';
    const FEATURE_AI_CHATBOT = 'ai_chatbot';
    const FEATURE_MESSAGING = 'messaging';
    const FEATURE_DOCUMENT_VERIFICATION = 'document_verification';
    const FEATURE_ADMISSION_LETTERS = 'admission_letters';
    const FEATURE_ANALYTICS = 'analytics';

    const FEATURES = [
        self::FEATURE_ADMISSIONS => 'Admissions Portal',
        self::FEATURE_ONLINE_EXAMS => 'Online Exams',
        self::FEATURE_FINANCE => 'Finance Module',
        self::FEATURE_LIBRARY => 'Library Module',
        self::FEATURE_NOTIFICATIONS => 'Notifications',
        self::FEATURE_AI_CHATBOT => 'AI Chatbot',
        self::FEATURE_MESSAGING => 'Messaging',
        self::FEATURE_DOCUMENT_VERIFICATION => 'Document Verification',
        self::FEATURE_ADMISSION_LETTERS => 'Admission Letters',
        self::FEATURE_ANALYTICS => 'Analytics',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isPaused(): bool
    {
        return $this->status === self::STATUS_PAUSED;
    }

    public function isDisabled(): bool
    {
        return $this->status === self::STATUS_DISABLED;
    }

    public function activate(): void
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'activated_at' => now(),
            'paused_at' => null,
            'deactivated_at' => null,
        ]);
    }

    public function pause(): void
    {
        $this->update([
            'status' => self::STATUS_PAUSED,
            'paused_at' => now(),
        ]);
    }

    public function deactivate(): void
    {
        $this->update([
            'status' => self::STATUS_DISABLED,
            'deactivated_at' => now(),
            'paused_at' => null,
        ]);
    }

    public function resume(): void
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'paused_at' => null,
        ]);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopePaused($query)
    {
        return $query->where('status', self::STATUS_PAUSED);
    }

    public function scopeDisabled($query)
    {
        return $query->where('status', self::STATUS_DISABLED);
    }

    public function scopeFeature($query, string $feature)
    {
        return $query->where('feature', $feature);
    }

    public static function isEnabled(int $schoolId, string $feature): bool
    {
        $schoolFeature = self::where('school_id', $schoolId)
            ->where('feature', $feature)
            ->first();

        return $schoolFeature && $schoolFeature->isActive();
    }

    public static function getEnabledFeatures(int $schoolId): array
    {
        return self::where('school_id', $schoolId)
            ->active()
            ->pluck('feature')
            ->toArray();
    }

    public static function hasAccess(int $schoolId, string $feature): bool
    {
        $school = School::find($schoolId);
        
        if (!$school) {
            return false;
        }

        $subscription = $school->activeSubscription;
        
        if (!$subscription) {
            return false;
        }

        $plan = $subscription->plan;
        
        if (!$plan) {
            return true;
        }

        $planFeatures = $plan->features ?? [];
        
        if (!in_array($feature, $planFeatures)) {
            return false;
        }

        return self::isEnabled($schoolId, $feature);
    }
}