<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotImpersonating
{
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('impersonate')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'This action is not allowed while impersonating another user.'
                ], 403);
            }
            
            return redirect()->back()->with('error', 'This action is not allowed while impersonating another user.');
        }

        return $next($request);
    }
}
