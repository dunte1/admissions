<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middleware = [
        \Illuminate\Http\Middleware\HandleCors::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    protected $middlewareGroups = [
        'web' => [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\HandleLanguage::class,
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\LogActivity::class,
        ],

        'api' => [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\SecurityHeaders::class,
        ],
    ];

    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \App\Http\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \App\Http\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \App\Http\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \App\Http\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        'role' => \App\Http\Middleware\RoleMiddleware::class,
        'permission' => \App\Http\Middleware\PermissionMiddleware::class,
        'data.scope' => \App\Http\Middleware\DataScopeMiddleware::class,
        'school.scope' => \App\Http\Middleware\SchoolScopeMiddleware::class,
        'subscription' => \App\Http\Middleware\CheckSubscription::class,
        'lang' => \App\Http\Middleware\HandleLanguage::class,
        'not.impersonating' => \App\Http\Middleware\EnsureNotImpersonating::class,
        'admin.rate' => \App\Http\Middleware\AdminActionRateLimit::class,
        'password.verified' => \App\Http\Middleware\RequirePasswordConfirmation::class,
        'security.headers' => \App\Http\Middleware\SecurityHeaders::class,
        'session.activity' => \App\Http\Middleware\SessionActivityTimeout::class,
        'log.activity' => \App\Http\Middleware\LogActivity::class,
    ];
}
