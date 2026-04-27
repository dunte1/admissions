<?php

namespace App\Multitenancy\Middleware;

use App\Models\School;
use App\Multitenancy\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $school = TenantManager::resolve($request);

        if (!$school) {
            $host = $request->getHost();
            
            if ($this->isMainPlatformDomain($host)) {
                return $next($request);
            }

            return $this->handleMissingTenant($request);
        }

        TenantManager::setCurrentSchool($school);

        return $next($request);
    }

    protected function isMainPlatformDomain(string $host): bool
    {
        $platformDomain = config('app.domain');
        $baseDomain = config('app.base_domain');
        $platformHost = config('app.url');
        
        $platformHostParsed = parse_url($platformHost, PHP_URL_HOST);

        if ($host === $platformDomain || $host === $baseDomain || $host === $platformHostParsed) {
            return true;
        }

        $subdomain = str_replace('.' . $baseDomain, '', $host);
        
        $mainSubdomains = ['www', 'admin', 'super-admin', 'superadmin', 'platform', 'api'];
        
        return in_array($subdomain, $mainSubdomains);
    }

    protected function handleMissingTenant(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'School not found. Please check the URL or contact support.',
            ], 404);
        }

        return redirect()->route('home')->with('error', 'School not found. Please check the URL.');
    }
}