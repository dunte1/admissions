<?php

namespace App\Http\Middleware;

use App\Models\School;
use App\Models\SchoolDomain;
use App\Multitenancy\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TenantScope
{
    public function handle(Request $request, Closure $next): Response
    {
        $school = $this->resolveSchoolFromRequest($request);

        if ($school) {
            if (!$school->isActive()) {
                return response()->view('errors.school-suspended', [
                    'school' => $school
                ], 403);
            }

            TenantManager::setCurrentSchool($school);
            app()->instance('current_school', $school);
            session(['current_school_id' => $school->id]);
        }

        return $next($request);
    }

    protected function resolveSchoolFromRequest(Request $request): ?School
    {
        $host = $request->getHost();

        $baseDomain = config('app.base_domain');
        
        if ($baseDomain && str_ends_with($host, '.' . $baseDomain)) {
            $subdomain = Str::before($host, '.' . $baseDomain);
            return $this->findBySubdomain($subdomain);
        }

        $primaryDomain = config('app.primary_domain');
        if ($primaryDomain && $host !== $primaryDomain && str_contains($host, '.')) {
            $subdomain = Str::before($host, '.' . $primaryDomain);
            if ($subdomain && !in_array($subdomain, ['www', 'admin', 'api'])) {
                return $this->findBySubdomain($subdomain);
            }
        }

        $domain = SchoolDomain::where('domain', $host)
            ->orWhere('domain', 'www.' . $host)
            ->first();

        if ($domain) {
            return $domain->school;
        }

        if ($request->has('school_id')) {
            return School::where('id', $request->school_id)
                ->where('status', 'active')
                ->first();
        }

        return null;
    }

    protected function findBySubdomain(string $subdomain): ?School
    {
        if (in_array($subdomain, ['www', 'admin', 'app', 'api', 'portal'])) {
            return null;
        }

        return School::where('subdomain', $subdomain)
            ->orWhere('slug', $subdomain)
            ->where('status', 'active')
            ->first();
    }
}