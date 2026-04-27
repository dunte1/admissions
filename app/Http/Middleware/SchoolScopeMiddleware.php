<?php

namespace App\Http\Middleware;

use App\Models\School;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SchoolScopeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        if ($user->hasRole('super_admin')) {
            return $next($request);
        }

        if (!$user->school_id) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'User is not associated with any school. Please contact administrator.'
                ], 403);
            }
            return redirect()->route('login')->with('error', 'No school association found');
        }

        $school = $user->school;
        
        if (!$school || !$school->isActive()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Your school account is inactive. Please contact administrator.'
                ], 403);
            }
            return redirect()->route('login')->with('error', 'School account is inactive');
        }

        $request->attributes->set('school', $school);
        $request->attributes->set('school_id', $school->id);
        session(['school_id' => $school->id]);

        app()->instance('current_school', $school);

        return $next($request);
    }
}