<?php

namespace App\Multitenancy\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TenantAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthenticated. Please login to continue.',
                ], 401);
            }

            return redirect()->route('tenant.login', [
                'school' => $request->attributes->get('school_id') ?? session('school_id')
            ]);
        }

        $user = Auth::user();

        if ($user->hasRole('super_admin')) {
            return $next($request);
        }

        if (!$user->school_id) {
            Auth::logout();
            $request->session()->invalidate();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'User is not associated with any school.',
                ], 403);
            }

            return redirect()->route('login')
                ->with('error', 'Your account is not associated with any school.');
        }

        $school = $user->school;
        
        if (!$school || !$school->isActive()) {
            Auth::logout();
            $request->session()->invalidate();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Your school account is inactive.',
                ], 403);
            }

            return redirect()->route('login')
                ->with('error', 'Your school account is inactive.');
        }

        return $next($request);
    }
}