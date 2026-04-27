<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class School extends Model
{
    use HasFactory;

    protected static ?int $currentId = null;

    protected $fillable = [
        'name',
        'code',
        'logo',
        'email',
        'phone',
        'address',
        'county',
        'town',
        'postal_code',
        'website',
        'facebook',
        'twitter',
        'instagram',
        'linkedin',
        'domain',
        'subdomain',
        'slug',
        'timezone',
        'currency',
        'currency_symbol',
        'status',
        'subscription_status',
        'trial_ends_at',
        'grace_ends_at',
        'description',
        'primary_color',
        'secondary_color',
        'favicon',
        'custom_footer_text',
        'enable_white_label',
        'admin_name',
        'admin_email',
        'admin_phone',
        'admissions_contact_name',
        'admissions_contact_email',
        'admissions_contact_phone',
        'finance_contact_name',
        'finance_contact_email',
        'finance_contact_phone',
        'notes',
        'signature_image',
        'seal_image',
        'signatory_name',
        'signatory_title',
        'admission_prefix',
        'admission_suffix',
        'admission_include_year',
        'admission_include_program_code',
        'admission_number_padding',
    ];

    protected $casts = [
        'status' => 'string',
        'subscription_status' => 'string',
        'logo' => 'string',
        'enable_white_label' => 'boolean',
        'trial_ends_at' => 'datetime',
        'grace_ends_at' => 'datetime',
    ];

    public static function setCurrentId(?int $id): void
    {
        self::$currentId = $id;
    }

    public static function getCurrentId(): ?int
    {
        if (self::$currentId !== null) {
            return self::$currentId;
        }
        
        $user = auth()->user();
        
        if (!$user) {
            return null;
        }

        if ($user->hasRole('super_admin')) {
            return null;
        }

        return $user->school_id;
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function admins(): HasMany
    {
        return $this->hasMany(User::class)->whereHas('roles', function ($query) {
            $query->whereIn('name', ['admin', 'super_admin', 'registrar', 'accountant']);
        });
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function intakes(): HasMany
    {
        return $this->hasMany(Intake::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function admissionLetters(): HasMany
    {
        return $this->hasMany(AdmissionLetter::class);
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->latest();
    }

    public function currentPlan(): BelongsTo
    {
        return $this->belongsToThrough(Plan::class, Subscription::class)
            ->where('subscriptions.status', 'active')
            ->where('subscriptions.expires_at', '>', now());
    }

    public function hasActiveSubscription(): bool
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->exists();
    }

    public function getSubscriptionStatusAttribute(): string
    {
        $subStatus = $this->attributes['subscription_status'] ?? null;

        if ($subStatus === 'trial' && $this->trial_ends_at?->isFuture()) {
            $daysLeft = now()->diffInDays($this->trial_ends_at);
            if ($daysLeft <= 7) {
                return 'trial_expiring';
            }
            return 'trial';
        }

        if ($subStatus === 'grace_period' && $this->grace_ends_at?->isFuture()) {
            return 'grace_period';
        }

        if ($this->hasActiveSubscription()) {
            $sub = $this->activeSubscription;
            if ($sub && now()->diffInDays($sub->expires_at) <= 7) {
                return 'expiring_soon';
            }
            return 'active';
        }

        if ($subStatus === 'grace_period' && $this->grace_ends_at?->isPast()) {
            return 'grace_expired';
        }

        if ($subStatus === 'trial' && $this->trial_ends_at?->isPast()) {
            return 'trial_expired';
        }

        return 'expired';
    }

    public function canAcceptNewApplications(): bool
    {
        return $this->isActive() && $this->hasActiveSubscription();
    }

    public function hasReachedStudentLimit(): bool
    {
        $subscription = $this->activeSubscription;
        if (!$subscription) {
            return true;
        }
        
        $plan = $subscription->plan;
        if (!$plan || !$plan->max_students) {
            return false;
        }
        
        $studentCount = $this->users()->whereHas('roles', fn($q) => $q->where('name', 'student'))->count();
        return $studentCount >= $plan->max_students;
    }

    public function currentIntake(): HasOne
    {
        return $this->hasOne(Intake::class)->where('is_current', true);
    }

    public function activeIntakes(): HasMany
    {
        return $this->hasMany(Intake::class)->where('is_active', true);
    }

    public function activePrograms(): HasMany
    {
        return $this->hasMany(Program::class)->where('is_active', true);
    }

    public function activeDepartments(): HasMany
    {
        return $this->hasMany(Department::class)->where('is_active', true);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function isOnTrial(): bool
    {
        return ($this->attributes['subscription_status'] ?? null) === 'trial' 
            && $this->trial_ends_at 
            && $this->trial_ends_at->isFuture();
    }

    public function isInGracePeriod(): bool
    {
        return ($this->attributes['subscription_status'] ?? null) === 'grace_period'
            && $this->grace_ends_at
            && $this->grace_ends_at->isFuture();
    }

    public function isSubscriptionActive(): bool
    {
        return ($this->attributes['subscription_status'] ?? null) === 'active';
    }

    public function isSubscriptionExpired(): bool
    {
        $subStatus = $this->attributes['subscription_status'] ?? null;
        return $subStatus === 'expired' 
            || ($subStatus === 'trial' && $this->trial_ends_at?->isPast())
            || ($subStatus === 'active' && !$this->hasActiveSubscription());
    }

    public function isFullyActive(): bool
    {
        return $this->status === 'active' 
            && ($this->isOnTrial() || $this->isSubscriptionActive() || $this->isInGracePeriod());
    }

    public function canAccessDashboard(): bool
    {
        return $this->status === 'active' 
            && in_array($this->attributes['subscription_status'] ?? null, ['trial', 'active', 'grace_period']);
    }

    public function canCreateApplications(): bool
    {
        return $this->canAccessDashboard() && !$this->isSubscriptionExpired();
    }

    public function canMakePayments(): bool
    {
        return $this->status === 'active' 
            && in_array($this->attributes['subscription_status'] ?? null, ['trial', 'active', 'grace_period']);
    }

    public function startTrial(int $days = 14, ?Plan $plan = null): Subscription
    {
        $this->update([
            'subscription_status' => 'trial',
            'trial_ends_at' => now()->addDays($days),
            'status' => 'active',
        ]);

        $plan = $plan ?? Plan::where('slug', 'starter')->first();

        return Subscription::create([
            'school_id' => $this->id,
            'plan_id' => $plan?->id,
            'status' => 'trial',
            'starts_at' => now(),
            'expires_at' => now()->addDays($days),
            'trial_ends_at' => now()->addDays($days),
            'is_trial' => true,
            'payment_method' => 'trial',
            'notes' => "Trial period: {$days} days",
        ]);
    }

    public function activateSubscription(Subscription $subscription): void
    {
        $this->update([
            'subscription_status' => 'active',
            'trial_ends_at' => null,
        ]);
    }

    public function startGracePeriod(): void
    {
        $subscription = $this->activeSubscription;
        $graceDays = $subscription?->plan?->grace_period_days ?? 3;

        $this->update([
            'subscription_status' => 'grace_period',
            'grace_ends_at' => now()->addDays($graceDays),
        ]);
    }

    public function suspend(): void
    {
        $this->update([
            'status' => 'suspended',
            'subscription_status' => 'suspended',
        ]);
    }

    public function reactivate(): void
    {
        $this->update([
            'status' => 'active',
            'subscription_status' => 'active',
            'grace_ends_at' => null,
        ]);
    }

    public function getDaysUntilExpiry(): ?int
    {
        if ($this->isOnTrial()) {
            return now()->diffInDays($this->trial_ends_at, false);
        }

        $subscription = $this->activeSubscription;
        if ($subscription) {
            return now()->diffInDays($subscription->expires_at, false);
        }

        return null;
    }

    public function getDaysUntilGraceExpiry(): ?int
    {
        if ($this->isInGracePeriod() && $this->grace_ends_at) {
            return now()->diffInDays($this->grace_ends_at, false);
        }
        return null;
    }

    public function getPrimaryContactEmail(): ?string
    {
        return $this->admin_email 
            ?? $this->admissions_contact_email 
            ?? $this->finance_contact_email 
            ?? $this->email;
    }

    public function getPrimaryContactName(): ?string
    {
        return $this->admin_name 
            ?? $this->admissions_contact_name 
            ?? $this->finance_contact_name;
    }

    public function getActiveIntakeAttribute(): ?Intake
    {
        return $this->intakes()->where('is_current', true)->first()
            ?? $this->intakes()->where('is_active', true)
                ->whereDate('application_end_date', '>=', now())
                ->orderBy('application_start_date', 'desc')
                ->first();
    }

    public function getConfig(string $key, $default = null)
    {
        $setting = $this->settings()->withoutGlobalScopes()->where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public function setConfig(string $key, $value, string $group = 'general'): void
    {
        $this->settings()->withoutGlobalScopes()->updateOrCreate(
            ['key' => $key, 'school_id' => $this->id],
            ['value' => $value, 'group' => $group]
        );
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    public function getLogoUrlAttribute(): string
    {
        if ($this->logo) {
            return asset("storage/{$this->logo}");
        }
        return asset('images/default-school.png');
    }

    public function getFaviconUrlAttribute(): string
    {
        if ($this->favicon) {
            return asset("storage/{$this->favicon}");
        }
        return asset('favicon.ico');
    }

    public function getPrimaryColorAttribute(): string
    {
        if ($this->attributes['primary_color']) {
            return $this->attributes['primary_color'];
        }
        return system_setting('primary_color', '#101092');
    }

    public function getSecondaryColorAttribute(): string
    {
        if ($this->attributes['secondary_color']) {
            return $this->attributes['secondary_color'];
        }
        return system_setting('secondary_color', '#10B981');
    }

    public function getFooterTextAttribute(): ?string
    {
        if ($this->custom_footer_text) {
            return $this->custom_footer_text;
        }
        return system_setting('footer_text', '© ' . date('Y') . ' ' . ($this->name ?? 'Institution') . '. Powered by Duncowebsolutions');
    }

    public function shouldShowFooter(): bool
    {
        if ($this->enable_white_label) {
            return false;
        }
        return system_setting('show_footer_branding', true);
    }

    public function getBranding(): array
    {
        return [
            'name' => $this->name ?? system_setting('system_name', config('app.name')),
            'logo' => $this->logoUrl,
            'favicon' => $this->faviconUrl,
            'primary_color' => $this->primaryColor,
            'secondary_color' => $this->secondaryColor,
            'footer_text' => $this->shouldShowFooter() ? $this->footerText : null,
        ];
    }

    public function getStatsAttribute(): array
    {
        return [
            'total_applications' => $this->applications()->count(),
            'pending_applications' => $this->applications()->where('status', 'pending')->count(),
            'approved_applications' => $this->applications()->where('status', 'approved')->count(),
            'total_users' => $this->users()->count(),
            'total_programs' => $this->programs()->count(),
            'total_revenue' => $this->payments()->where('status', 'completed')->sum('amount'),
        ];
    }

    public function getSignatureImageUrlAttribute(): ?string
    {
        if ($this->signature_image) {
            return asset("storage/{$this->signature_image}");
        }
        return null;
    }

    public function getSealImageUrlAttribute(): ?string
    {
        if ($this->seal_image) {
            return asset("storage/{$this->seal_image}");
        }
        return null;
    }

    public function getSignatureNameAttribute(): ?string
    {
        return $this->signatory_name ?? $this->admissions_contact_name ?? 'Admissions Office';
    }

    public function getSignatureTitleAttribute(): ?string
    {
        return $this->signatory_title ?? 'Admissions Officer';
    }

    protected static function booted(): void
    {
        static::creating(function (School $school) {
            if (empty($school->slug) && !empty($school->name)) {
                $school->slug = \Illuminate\Support\Str::slug($school->name);
            }
        });

        static::updating(function (School $school) {
            if ($school->isDirty('name') && !$school->isDirty('slug')) {
                $school->slug = \Illuminate\Support\Str::slug($school->name);
            }
        });
    }
}