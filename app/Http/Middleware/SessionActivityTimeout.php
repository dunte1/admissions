<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class SessionActivityTimeout
{
    protected const TIMEOUT_SECONDS = 900;

    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return $next($request);
        }

        $userId = $request->user()->id;
        $lastActivityKey = "user_last_activity_{$userId}";
        $timeoutSeconds = config('session.lifetime', self::TIMEOUT_SECONDS) * 60;

        $lastActivity = Cache::get($lastActivityKey);

        if ($lastActivity && (time() - $lastActivity) > $timeoutSeconds) {
            Cache::forget($lastActivityKey);
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Session expired due to inactivity.'], 401);
            }

            return redirect()->route('login')->with('error', 'Your session has expired due to inactivity. Please login again.');
        }

        Cache::put($lastActivityKey, time(), $timeoutSeconds + 60);

        return $next($request);
    }
}
