<?php

namespace App\Models;

use App\Traits\TwoFactorAuthenticatable;
use App\Traits\SchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, TwoFactorAuthenticatable, SchoolScope;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'role',
        'school_id',
        'is_active',
        'email_verified_at',
        'verification_token',
        'otp_code',
        'otp_expires_at',
        'preferred_language',
        'dark_mode',
        'photo',
        'user_settings',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_expires_at' => 'datetime',
        'is_active' => 'boolean',
        'dark_mode' => 'boolean',
        'is_verified' => 'boolean',
        'is_first_login' => 'boolean',
        'phone_verified_at' => 'datetime',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin') || $this->hasRole('super_admin');
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function isStaff(): bool
    {
        return $this->hasAnyRole(['admin', 'registrar', 'accountant', 'reviewer', 'support']);
    }

    public function isStudent(): bool
    {
        return $this->hasRole('student');
    }

    public function hasRole($roles, ?string $guard = null): bool
    {
        if ($roles instanceof \Illuminate\Support\Collection) {
            foreach ($roles as $role) {
                if ($this->hasRole($role, $guard)) {
                    return true;
                }
            }
            return false;
        }

        $this->loadMissing('roles');

        if (is_string($roles) && strpos($roles, '|') !== false) {
            $roles = $this->convertPipeToArray($roles);
        }

        if ($roles instanceof \BackedEnum) {
            $roles = $roles->value;
            return $this->roles
                ->when($guard, fn ($q) => $q->where('guard_name', $guard))
                ->pluck('name')
                ->contains(fn ($name) => $name instanceof \BackedEnum ? $name->value == $roles : $name == $roles);
        }

        if (is_int($roles)) {
            return $guard
                ? $this->roles->where('guard_name', $guard)->contains($this->getKeyName(), $roles)
                : $this->roles->contains($this->getKeyName(), $roles);
        }

        if ($roles instanceof \Spatie\Permission\Models\Role) {
            return $this->roles->contains($roles->getKeyName(), $roles->getKey());
        }

        if (is_string($roles)) {
            return $guard
                ? $this->roles->where('guard_name', $guard)->contains('name', $roles)
                : $this->roles->contains('name', $roles);
        }

        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role, $guard)) {
                    return true;
                }
            }
            return false;
        }

        return false;
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->getRoleNames()->intersect($roles)->isNotEmpty();
    }

    public function hasAllRoles(array $roles): bool
    {
        return $this->getRoleNames()->containsAll($roles);
    }

    public function roles(): BelongsToMany
    {
        $relation = $this->morphToMany(
            config('permission.models.role'),
            'model',
            config('permission.table_names.model_has_roles'),
            config('permission.column_names.model_morph_key'),
            app(\Spatie\Permission\PermissionRegistrar::class)->pivotRole
        );

        return $relation->withoutGlobalScopes();
    }

    public function permissions(): BelongsToMany
    {
        $relation = $this->morphToMany(
            config('permission.models.permission'),
            'model',
            config('permission.table_names.model_has_permissions'),
            config('permission.column_names.model_morph_key'),
            app(\Spatie\Permission\PermissionRegistrar::class)->pivotPermission
        );

        return $relation->withoutGlobalScopes();
    }

    public function generateOTP(): string
    {
        $this->otp_code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->otp_expires_at = now()->addMinutes(15);
        $this->save();
        return $this->otp_code;
    }

    public function clearOTP(): void
    {
        $this->otp_code = null;
        $this->otp_expires_at = null;
        $this->save();
    }

    public function fullName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getProfileCompletion(): int
    {
        $fields = ['first_name', 'last_name', 'email', 'phone'];
        $completed = 0;
        $total = count($fields);

        foreach ($fields as $field) {
            if (!empty($this->$field)) {
                $completed++;
            }
        }

        return round(($completed / $total) * 100);
    }

    public function canAccessPanel(string $panel): bool
    {
        $panelPermissions = [
            'admin' => ['view_applications', 'view_payments', 'view_users'],
            'student' => ['create_application', 'view_application'],
        ];

        $permissions = $panelPermissions[$panel] ?? [];
        return $this->hasAnyPermission($permissions);
    }

    public function getPermissionsByGroup(): array
    {
        $permissions = $this->getAllPermissions()->groupBy('group');
        $grouped = [];

        foreach ($permissions as $group => $permissionCollection) {
            $grouped[$group] = $permissionCollection->pluck('name')->toArray();
        }

        return $grouped;
    }

    public function hasPermissionToAccessResource(string $permission, $resource): bool
    {
        if ($this->hasPermissionTo($permission)) {
            return true;
        }

        if ($this->hasRole('student') && $resource instanceof \App\Models\Application) {
            return $resource->user_id === $this->id;
        }

        return false;
    }

    protected $userSettings = [];

    public function getSetting(string $key, $default = null)
    {
        if (empty($this->userSettings)) {
            $this->loadUserSettings();
        }
        
        return $this->userSettings[$key] ?? $default;
    }

    public function setSetting(string $key, $value): self
    {
        $this->userSettings[$key] = $value;
        $this->saveUserSettings();
        return $this;
    }

    public function setSettings(array $settings): self
    {
        foreach ($settings as $key => $value) {
            $this->userSettings[$key] = $value;
        }
        $this->saveUserSettings();
        return $this;
    }

    protected function loadUserSettings(): void
    {
        $settings = $this->getAttribute('user_settings');
        if ($settings) {
            $this->userSettings = is_array($settings) ? $settings : json_decode($settings, true) ?? [];
        } else {
            $this->userSettings = [];
        }
    }

    protected function saveUserSettings(): void
    {
        $this->updateQuietly(['user_settings' => $this->userSettings]);
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->photo) {
            return asset("storage/{$this->photo}");
        }
        
        return "https://ui-avatars.com/api/?name=" . urlencode($this->fullName()) . "&color=7C3AED&background=F3E8FF";
    }

    public function getInitialsAttribute(): string
    {
        return substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1);
    }

    public function logoutFromAllDevices(): void
    {
        $this->setSetting('force_logout', now()->timestamp);
        $this->tokens()->delete();
    }

    public function shouldForceLogout(): bool
    {
        $forceLogout = $this->getSetting('force_logout');
        if (!$forceLogout) {
            return false;
        }

        if (auth()->check() && auth()->user()->id === $this->id) {
            $lastToken = auth()->user()->currentAccessToken();
            if ($lastToken) {
                $tokenCreatedAt = $lastToken->created_at->timestamp;
                if ($tokenCreatedAt < $forceLogout) {
                    return true;
                }
            }
        }

        return false;
    }

    public function isVerified(): bool
    {
        return $this->is_verified || $this->email_verified_at !== null;
    }

    public function isFirstLogin(): bool
    {
        return $this->is_first_login;
    }

    public function markAsVerified(): bool
    {
        $this->update([
            'is_verified' => true,
            'last_verified_at' => now(),
            'is_first_login' => false,
        ]);
        return true;
    }

    public function markFirstLoginComplete(): bool
    {
        $this->update(['is_first_login' => false]);
        return true;
    }

    public function generatePhoneVerificationToken(): string
    {
        $token = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->update(['phone_verification_token' => hash('sha256', $token)]);
        return $token;
    }

    public function verifyPhone(string $token): bool
    {
        if (hash_equals($this->phone_verification_token ?? '', hash('sha256', $token))) {
            $this->update([
                'phone_verified_at' => now(),
                'phone_verification_token' => null,
            ]);
            return true;
        }
        return false;
    }

    public function hasVerifiedPhone(): bool
    {
        return $this->phone_verified_at !== null;
    }

    public function sendVerificationNotification(): void
    {
        $this->notify(new \App\Notifications\VerifyEmail());
    }

    public function sendPhoneVerificationCode(): string
    {
        return $this->generatePhoneVerificationToken();
    }
}
