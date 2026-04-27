<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DataScopeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        if ($user->hasRole('student')) {
            $request->merge(['scope_user_id' => $user->id]);
            $request->attributes->set('scope_user_id', $user->id);
        }

        return $next($request);
    }
}