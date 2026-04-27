<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth as BaseAuthenticateWithBasicAuth;

class AuthenticateWithBasicAuth extends BaseAuthenticateWithBasicAuth
{
    protected function challenge($request)
    {
        return response('Invalid credentials.', 401);
    }
}
