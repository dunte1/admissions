<?php

namespace App\Traits;

use App\Models\ActivityLog;
use App\Models\AuditLog;

trait Auditable
{
    protected static function bootAuditable(): void
    {
        static::created(function ($model) {
            static::auditLog('create', $model, null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $changed = $model->getChanges();
            unset($changed['updated_at']);
            if (!empty($changed)) {
                static::auditLog('update', $model, $model->getOriginal(), $changed);
            }
        });

        static::deleted(function ($model) {
            static::auditLog('delete', $model, $model->getAttributes(), null);
        });
    }

    protected static function auditLog(string $action, $model, ?array $oldValues = null, ?array $newValues = null): void
    {
        if (!auth()->check()) {
            return;
        }

        $excludedFields = ['created_at', 'updated_at', 'remember_token'];
        $oldValues = $oldValues ? array_diff_key($oldValues, array_flip($excludedFields)) : null;
        $newValues = $newValues ? array_diff_key($newValues, array_flip($excludedFields)) : null;

        $modelName = class_basename($model);
        $module = static::getAuditModule();

        try {
            AuditLog::log(
                action: $action,
                model: $model,
                oldValues: $oldValues,
                newValues: $newValues
            );
        } catch (\Throwable $e) {
            // Silently fail if AuditLog table doesn't exist
        }

        try {
            ActivityLog::logAction(
                action: $action,
                module: $module,
                description: ucfirst($action) . " {$modelName}" . ($model->id ? " #{$model->id}" : ''),
                metadata: [
                    'model' => get_class($model),
                    'model_id' => $model->id,
                    'changes' => $newValues,
                ]
            );
        } catch (\Throwable $e) {
            // Silently fail if ActivityLog table doesn't exist
        }
    }

    protected static function getAuditModule(): string
    {
        return 'models';
    }

    public function getAuditIdentifier(): string
    {
        return class_basename($this) . '#' . $this->id;
    }
}
