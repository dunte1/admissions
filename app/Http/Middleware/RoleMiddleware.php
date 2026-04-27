<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        if (!$user->hasAnyRole($roles)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Access denied.'], 403);
            }
            
            toastr()->error('Access denied.');
            
            if ($user->hasRole('super_admin')) {
                return redirect()->route('super-admin.dashboard');
            }
            
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
