<?php

namespace App\Services;

use App\Models\School;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SettingsService
{
    const GROUP_GENERAL = 'general';
    const GROUP_ADMISSION = 'admission';
    const GROUP_PAYMENT = 'payment';
    const GROUP_EMAIL = 'email';
    const GROUP_SMS = 'sms';
    const GROUP_SECURITY = 'security';
    const GROUP_NOTIFICATION = 'notification';
    const GROUP_BRANDING = 'branding';
    const GROUP_DOCUMENT = 'document';
    const GROUP_SEO = 'seo';

    const CACHE_KEY = 'settings_cache';
    const CACHE_TTL = 3600;

    protected ?int $schoolId = null;

    public function __construct(?int $schoolId = null)
    {
        $this->schoolId = $schoolId ?? School::getCurrentId();
    }

    public function get(string $key, $default = null)
    {
        $settings = $this->all();
        return $settings[$key] ?? $default;
    }

    public function set(string $key, $value, string $group = self::GROUP_GENERAL): self
    {
        $this->getQuery()
            ->updateOrCreate(
                ['key' => $key, 'school_id' => $this->schoolId],
                ['value' => $value, 'group' => $group]
            );

        $this->clearCache();

        return $this;
    }

    public function setMany(array $settings, string $group = self::GROUP_GENERAL): self
    {
        foreach ($settings as $key => $value) {
            $this->set($key, $value, $group);
        }

        return $this;
    }

    public function getByGroup(string $group): array
    {
        return $this->getQuery()
            ->where('group', $group)
            ->pluck('value', 'key')
            ->toArray();
    }

    public function all(): array
    {
        $schoolId = $this->schoolId;
        $cacheKey = self::CACHE_KEY . ($schoolId ? "_{$schoolId}" : '_global');

        return Cache::remember($cacheKey, self::CACHE_TTL, function () {
            return $this->getQuery()
                ->pluck('value', 'key')
                ->toArray();
        });
    }

    public function clearCache(): void
    {
        $schoolId = $this->schoolId;
        Cache::forget(self::CACHE_KEY . ($schoolId ? "_{$schoolId}" : '_global'));
        Cache::forget(self::CACHE_KEY . '_global');
    }

    public function delete(string $key): bool
    {
        $deleted = $this->getQuery()->where('key', $key)->delete() > 0;

        if ($deleted) {
            $this->clearCache();
        }

        return $deleted;
    }

    public function exists(string $key): bool
    {
        return $this->getQuery()->where('key', $key)->exists();
    }

    public static function getGlobal(string $key, $default = null)
    {
        return (new self(null))->get($key, $default);
    }

    public static function setGlobal(string $key, $value, string $group = self::GROUP_GENERAL): void
    {
        (new self(null))->set($key, $value, $group);
    }

    public static function getSchool(?int $schoolId): self
    {
        return new self($schoolId);
    }

    protected function getQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = $this->schoolId
            ? Setting::where(function ($q) {
                $q->where('school_id', $this->schoolId);
            })
            : Setting::withoutGlobalScope(\App\Scopes\SchoolScope::class)
                ->where(function ($q) {
                    $q->whereNull('school_id');
                });

        return $query;
    }

    public static function getDefaults(): array
    {
        return [
            'app_name' => config('app.name'),
            'app_url' => config('app.url'),
            'timezone' => 'Africa/Nairobi',
            'locale' => 'en',
            'currency' => 'KES',
            'currency_symbol' => 'KSh',
            'application_fee' => 2000,
            'application_start_date' => '',
            'application_end_date' => '',
            'max_applications_per_student' => 3,
            'admissions_open' => true,
            'admission_fee_amount' => 5000,
            'commitment_fee_amount' => 2000,
            'payment_mpesa_enabled' => false,
            'payment_paypal_enabled' => false,
            'payment_card_enabled' => false,
            'payment_manual_enabled' => true,
            'payment_bank_enabled' => true,
            'mpesa_shortcode' => '',
            'mpesa_passkey' => '',
            'bank_name' => '',
            'bank_account_name' => '',
            'bank_account_number' => '',
            'bank_branch' => '',
            'email_notifications_enabled' => true,
            'sms_notifications_enabled' => false,
            'require_document_verification' => true,
            'allow_multiple_programs' => true,
            'require_payment_before_submission' => true,
            'dark_mode_enabled' => true,
            'primary_color' => '#7C3AED',
            'enable_admissions' => true,
            'enable_payments' => true,
            'enable_notifications' => true,
            'enable_multi_school' => true,
            'enable_student_registration' => true,
            'enable_document_upload' => true,
            'enable_sms_reminders' => false,
        ];
    }

    public static function getSettingGroups(): array
    {
        return [
            'general' => [
                'label' => 'General',
                'icon' => 'cog',
                'description' => 'Basic system configuration',
                'roles' => ['super_admin', 'admin'],
            ],
            'features' => [
                'label' => 'System Features',
                'icon' => 'toggle',
                'description' => 'Enable/disable system modules',
                'roles' => ['super_admin'],
            ],
            'admission' => [
                'label' => 'Admission',
                'icon' => 'clipboard',
                'description' => 'Admission periods and requirements',
                'roles' => ['super_admin', 'admin', 'registrar'],
            ],
            'payment' => [
                'label' => 'Payment',
                'icon' => 'credit-card',
                'description' => 'Payment gateway configuration',
                'roles' => ['super_admin', 'admin', 'accountant'],
            ],
            'email' => [
                'label' => 'Email / SMS',
                'icon' => 'mail',
                'description' => 'Notification channels',
                'roles' => ['super_admin', 'admin'],
            ],
            'security' => [
                'label' => 'Security',
                'icon' => 'shield',
                'description' => 'Security and access settings',
                'roles' => ['super_admin', 'admin'],
            ],
            'branding' => [
                'label' => 'Branding',
                'icon' => 'palette',
                'description' => 'School branding and theme',
                'roles' => ['super_admin', 'admin'],
            ],
            'document' => [
                'label' => 'Documents',
                'icon' => 'document',
                'description' => 'Document requirements',
                'roles' => ['super_admin', 'admin', 'registrar'],
            ],
            'seo' => [
                'label' => 'SEO',
                'icon' => 'search',
                'description' => 'Search engine optimization settings',
                'roles' => ['super_admin'],
            ],
        ];
    }

    public function canAccessGroup(string $group): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        if ($user->hasRole('super_admin')) {
            return true;
        }

        $groups = self::getSettingGroups();
        $allowedRoles = $groups[$group]['roles'] ?? [];

        foreach ($allowedRoles as $role) {
            if ($user->hasRole($role)) {
                return true;
            }
        }

        return false;
    }
}
