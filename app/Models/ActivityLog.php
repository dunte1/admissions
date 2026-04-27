<?php

namespace App\Models;

use App\Traits\SchoolScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use SchoolScope;

    public $timestamps = false;

    protected $fillable = [
        'school_id',
        'user_id',
        'role',
        'action',
        'module',
        'description',
        'ip_address',
        'user_agent',
        'url',
        'method',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function log(
        string $action,
        ?string $module = null,
        ?string $description = null,
        ?array $metadata = null
    ): self {
        $user = auth()->user();

        return self::create([
            'school_id' => $user?->school_id,
            'user_id' => $user?->id,
            'role' => $user?->roles->first()?->name,
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'metadata' => $metadata,
        ]);
    }

    public static function logPageView(string $module, string $description): self
    {
        return self::log('view', $module, $description, [
            'type' => 'page_view',
        ]);
    }

    public static function logLogin(): self
    {
        return self::log('login', 'auth', 'User logged in');
    }

    public static function logLogout(): self
    {
        return self::log('logout', 'auth', 'User logged out');
    }

    public static function logViewRecord(string $module, string $recordType, int $recordId): self
    {
        return self::log('view', $module, "Viewed {$recordType} #{$recordId}", [
            'record_type' => $recordType,
            'record_id' => $recordId,
        ]);
    }

    public static function logAction(string $action, string $module, string $description, ?array $metadata = null): self
    {
        return self::log($action, $module, $description, $metadata);
    }

    public function scopeForModule($query, string $module)
    {
        return $query->where('module', $module);
    }

    public function scopeForAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeAuth($query)
    {
        return $query->where('user_id', auth()->id());
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
