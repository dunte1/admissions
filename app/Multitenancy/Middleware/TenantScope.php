<?php

namespace App\Multitenancy\Middleware;

use App\Multitenancy\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantScope
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->hasRole('super_admin')) {
            return $next($request);
        }

        $schoolId = TenantManager::getSchoolId();

        if (!$schoolId) {
            $tenant = TenantManager::resolve($request);
            
            if ($tenant) {
                $schoolId = $tenant->id;
            }
        }

        if (!$schoolId && $user?->school_id) {
            $schoolId = $user->school_id;
        }

        if ($schoolId) {
            $request->attributes->set('school_id', $schoolId);
            $request->attributes->set('tenant_id', $schoolId);
            
            TenantManager::setCurrentSchool(
                TenantManager::getCurrentSchool()
            );
        }

        return $next($request);
    }
}