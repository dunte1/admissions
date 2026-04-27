<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\RequirePassword as BaseRequirePassword;

class RequirePassword extends BaseRequirePassword
{
    protected function redirectTo(Request $request)
    {
        return $request->expectsJson() ? null : route('login');
    }
}
