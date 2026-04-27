<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait DataScopeTrait
{
    protected function applyDataScope(Builder $query): Builder
    {
        $user = auth()->user();

        if ($user->hasRole('student')) {
            return $query->where('user_id', $user->id);
        }

        if ($user->hasRole('reviewer')) {
            return $query->whereIn('status', ['pending', 'under_review']);
        }

        if ($user->hasRole('accountant')) {
            return $query->whereHas('payment', function ($q) {
                $q->where('status', 'completed');
            });
        }

        return $query;
    }

    public function scopeForUser($query, $user = null): Builder
    {
        $user = $user ?? auth()->user();

        if (!$user) {
            return $query;
        }

        if ($user->hasRole('super_admin') || $user->hasRole('admin')) {
            return $query;
        }

        if ($user->hasRole('student')) {
            return $query->where('user_id', $user->id);
        }

        return $query;
    }
}