<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $permissions = explode('|', $permission);

        if (!$user->hasAnyPermission($permissions)) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            toastr()->error(__('You do not have permission to perform this action.'));
            
            if ($user->hasRole('super_admin')) {
                return redirect()->route('super-admin.dashboard');
            }
            
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}