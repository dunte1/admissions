<?php

namespace App\Models;

use App\Traits\SchoolScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory, SchoolScope;

    protected $fillable = [
        'school_id',
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auditable()
    {
        $modelType = $this->model_type;
        if ($modelType && class_exists($modelType)) {
            return $modelType::find($this->model_id);
        }
        return null;
    }

    public function getDescriptionAttribute(): string
    {
        $user = $this->user;
        $userName = $user ? $user->fullName() : 'System';
        
        return match($this->action) {
            'create' => "{$userName} created a new record",
            'update' => "{$userName} updated a record",
            'delete' => "{$userName} deleted a record",
            'approve_application' => "{$userName} approved application #{$this->model_id}",
            'reject_application' => "{$userName} rejected application #{$this->model_id}",
            'request_info' => "{$userName} requested more info for application #{$this->model_id}",
            'bulk_approve' => "{$userName} bulk approved applications",
            'bulk_reject' => "{$userName} bulk rejected applications",
            'bulk_request_info' => "{$userName} bulk requested info",
            'bulk_delete' => "{$userName} bulk deleted applications",
            'login' => "{$userName} logged in",
            'logout' => "{$userName} logged out",
            'update_settings' => "{$userName} updated settings",
            default => "{$userName} performed action: {$this->action}",
        };
    }

    public static function log(string $action, $model = null, ?array $oldValues = null, ?array $newValues = null): self
    {
        $schoolId = null;
        
        $user = auth()->user();
        if ($user) {
            $schoolId = $user->school_id;
            
            if ($model && $model instanceof School) {
                $schoolId = $model->id;
            } elseif ($model && $model->school_id) {
                $schoolId = $model->school_id;
            }
        }
        
        return self::create([
            'school_id' => $schoolId,
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
