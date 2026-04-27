<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class AdminActionRateLimit
{
    protected array $limitedActions = [
        'admin.applications.bulk-action',
        'admin.applications.approve',
        'admin.applications.reject',
        'admin.users.reset-password',
        'admin.notifications.send-bulk',
    ];

    protected int $maxAttempts = 10;
    protected int $decayMinutes = 5;

    public function handle(Request $request, Closure $next, ?string $action = null): Response
    {
        $routeName = $request->route()?->getName();
        
        if ($action) {
            $key = $this->resolveRequestKey($action);
        } elseif ($routeName && in_array($routeName, $this->limitedActions)) {
            $key = $this->resolveRequestKey($routeName);
        } else {
            return $next($request);
        }

        if (RateLimiter::tooManyAttempts($key, $this->maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => "Too many requests. Please try again in $seconds seconds.",
                    'retry_after' => $seconds,
                ], 429);
            }
            
            return redirect()->back()
                ->with('error', "Too many requests. Please try again in $seconds seconds.")
                ->withHeaders([
                    'Retry-After' => $seconds,
                    'X-RateLimit-Limit' => $this->maxAttempts,
                    'X-RateLimit-Remaining' => RateLimiter::remaining($key, $this->maxAttempts),
                ]);
        }

        RateLimiter::hit($key, $this->decayMinutes * 60);

        $response = $next($request);

        if ($response instanceof Response) {
            $response->headers->set('X-RateLimit-Limit', $this->maxAttempts);
            $response->headers->set('X-RateLimit-Remaining', RateLimiter::remaining($key, $this->maxAttempts));
        }

        return $response;
    }

    protected function resolveRequestKey(string $action): string
    {
        $userId = auth()->id() ?? 'guest';
        return "admin_action:{$action}:{$userId}";
    }
}
