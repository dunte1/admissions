<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\ThrottleRequestsException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
        'credit_card',
        'secret',
    ];

    protected $dontReport = [
        AuthenticationException::class,
        ValidationException::class,
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            if (config('app.env') === 'production') {
                $this->logException($e);
            }
        });

        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found',
                    'error' => $e->getMessage(),
                ], 404);
            }
        });

        $this->renderable(function (AccessDeniedHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied',
                ], 403);
            }
        });

        $this->renderable(function (ThrottleRequestsException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many attempts. Please slow down.',
                    'retry_after' => $e->getHeaders()['Retry-After'] ?? 60,
                ], 429);
            }
        });

        $this->renderable(function (ModelNotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found',
                ], 404);
            }
        });
    }

    protected function logException(Throwable $e): void
    {
        $context = [
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'ip' => request()->ip(),
            'user_id' => auth()->id(),
        ];

        if ($e instanceof \Illuminate\Validation\ValidationException) {
            return;
        }

        if ($e instanceof \Illuminate\Auth\AuthenticationException) {
            return;
        }

        if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            \Log::channel('daily')->warning('404 Not Found', $context);
            return;
        }

        \Log::channel('daily')->error('Application Error', $context);
    }

    protected function shouldReturnJson($request, Throwable $e): bool
    {
        if ($request->expectsJson()) {
            return true;
        }

        return parent::shouldReturnJson($request, $e);
    }
}
