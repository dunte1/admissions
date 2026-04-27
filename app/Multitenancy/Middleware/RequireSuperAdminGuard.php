<?php

namespace App\Multitenancy\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

class RequireSuperAdminGuard
{
    protected array $superAdminRoutes = [
        'super-admin.',
        'platform.',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return $this->redirectToLogin($request);
        }

        if (!$request->user()->hasAnyRole(['super_admin', 'platform_admin'])) {
            return $this->handleUnauthorized($request);
        }

        return $next($request);
    }

    protected function redirectToLogin(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Unauthenticated. Please login as Super Admin or Platform Admin.',
            ], 401);
        }

        return redirect()->route('super-admin.login');
    }

    protected function handleUnauthorized(Request $request): Response
    {
        auth()->logout();
        $request->session()->invalidate();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Access denied. This area is for Super Admins only.',
            ], 403);
        }

        return redirect()->route('super-admin.login')
            ->with('error', 'Access denied. This area is for Super Admins only.');
    }
}