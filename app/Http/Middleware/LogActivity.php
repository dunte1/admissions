<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    private const EXCLUDED_ROUTES = [
        'health',
        'up',
        'sanctum.csrf-cookie',
        'ignition.*',
        'debugbar.*',
    ];

    private const EXCLUDED_PREFIXES = [
        '_debugbar',
        'telescope',
    ];

    private const EXCLUDED_METHODS = [
        'HEAD',
    ];

    private const MODULE_MAPPING = [
        'admin/applications' => 'Applications',
        'admin/users' => 'Users',
        'admin/programs' => 'Programs',
        'admin/intakes' => 'Intakes',
        'admin/roles' => 'Roles & Permissions',
        'admin/settings' => 'Settings',
        'admin/documents' => 'Documents',
        'admin/audit-logs' => 'Audit Logs',
        'student/applications' => 'Applications',
        'student/profile' => 'Profile',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $this->logActivity($request, $response);

        return $response;
    }

    private function logActivity(Request $request, Response $response): void
    {
        if (!$this->shouldLog($request, $response)) {
            return;
        }

        $module = $this->determineModule($request);
        $action = $this->determineAction($request, $response);
        $description = $this->generateDescription($request, $action, $module);

        try {
            ActivityLog::log(
                action: $action,
                module: $module,
                description: $description,
                metadata: [
                    'method' => $request->method(),
                    'status_code' => $response->getStatusCode(),
                    'route' => $request->route()?->getName(),
                ]
            );
        } catch (\Throwable $e) {
            // Silently fail if logging fails
        }
    }

    private function shouldLog(Request $request, Response $response): bool
    {
        if (!auth()->check()) {
            return false;
        }

        if (in_array($request->method(), self::EXCLUDED_METHODS)) {
            return false;
        }

        if ($response->getStatusCode() >= 400) {
            return false;
        }

        foreach (self::EXCLUDED_ROUTES as $pattern) {
            if ($request->routeIs($pattern)) {
                return false;
            }
        }

        foreach (self::EXCLUDED_PREFIXES as $prefix) {
            if (str_starts_with($request->path(), $prefix)) {
                return false;
            }
        }

        return true;
    }

    private function determineModule(Request $request): string
    {
        $path = $request->path();
        $path = ltrim($path, '/');

        foreach (self::MODULE_MAPPING as $pattern => $module) {
            if (str_starts_with($path, $pattern)) {
                return $module;
            }
        }

        $segments = explode('/', $path);
        if (count($segments) >= 2) {
            return ucfirst($segments[1]) ?: 'General';
        }

        return 'General';
    }

    private function determineAction(Request $request, Response $response): string
    {
        $method = strtolower($request->method());

        if ($method === 'get') {
            $routeName = $request->route()?->getName() ?? '';
            if (str_contains($routeName, 'show') || str_contains($routeName, 'edit')) {
                return 'view';
            }
            if (str_contains($routeName, 'index')) {
                return 'browse';
            }
            return 'view';
        }

        return match ($method) {
            'post' => 'create',
            'put', 'patch' => 'update',
            'delete' => 'delete',
            default => $method,
        };
    }

    private function generateDescription(Request $request, string $action, string $module): string
    {
        $user = auth()->user();
        $userName = $user?->fullName() ?? 'User';

        $routeName = $request->route()?->getName();
        if ($routeName) {
            $routeLabel = str_replace(['admin.', 'student.', 'super-admin.'], '', $routeName);
            $routeLabel = str_replace('.', ' ', $routeLabel);
            $routeLabel = ucwords($routeLabel);
            return "{$userName} {$action}ed {$module} ({$routeLabel})";
        }

        return "{$userName} {$action}ed {$module}";
    }
}
