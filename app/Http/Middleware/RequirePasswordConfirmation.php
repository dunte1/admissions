<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class RequirePasswordConfirmation
{
    protected array $protectedRoutes = [
        'admin.users.destroy',
        'admin.roles.destroy',
        'admin.programs.destroy',
        'admin.intakes.destroy',
        'admin.settings.update',
        'admin.subscription.cancel',
    ];

    public function handle(Request $request, Closure $next, ?string $routeName = null): Response
    {
        $currentRoute = $request->route()?->getName();
        
        // Check if current route requires password confirmation
        if ($currentRoute && in_array($currentRoute, $this->protectedRoutes)) {
            return $this->verifyPassword($request, $next);
        }
        
        // Check if route name was passed as parameter
        if ($routeName && $request->routeIs($routeName)) {
            return $this->verifyPassword($request, $next);
        }

        return $next($request);
    }

    protected function verifyPassword(Request $request, Closure $next): Response
    {
        $password = $request->input('password_confirmation');
        
        if (!$password) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Password confirmation required for this action.',
                    'requires_password_confirmation' => true,
                ], 403);
            }
            
            return redirect()->back()->with('error', 'Password confirmation required for this sensitive action.');
        }

        $user = $request->user();
        
        if (!$user || !Hash::check($password, $user->password)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Invalid password confirmation.',
                ], 403);
            }
            
            return redirect()->back()->with('error', 'Invalid password confirmation.');
        }

        return $next($request);
    }
}
