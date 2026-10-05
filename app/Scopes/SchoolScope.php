<?php

namespace App\Scopes;

use App\Models\School;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class SchoolScope implements Scope
{
    public function apply(Builder $query, Model $model): void
    {
        // Skip scope for User model during authentication
        if ($model instanceof \App\Models\User) {
            return;
        }

        // Super admin bypasses school scope completely
        if (function_exists('auth') && auth()->check()) {
            $user = auth()->user();
            if ($user && $user->hasRole('super_admin')) {
                return;
            }
        }

        // Check session first (set by SchoolScopeMiddleware)
        $schoolId = session('school_id');
        
        // Fall back to auth user
        if (!$schoolId && function_exists('auth') && auth()->check()) {
            $user = auth()->user();
            if ($user) {
                $schoolId = $user->school_id;
            }
        }

        if (!$schoolId) {
            // Allow console commands and tests to operate without school context
            if (!app()->runningInConsole()) {
                // Prevent data leakage on misconfigured routes
                $query->whereRaw('1 = 0');
            }
            return;
        }

        $query->where('school_id', $schoolId);
    }
}