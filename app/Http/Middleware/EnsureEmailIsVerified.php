<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified as BaseEnsureEmailIsVerified;

class EnsureEmailIsVerified extends BaseEnsureEmailIsVerified
{
    public static function redirectTo($request)
    {
        return $request->expectsJson() ? null : route('login');
    }
}
