<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authorize as BaseAuthorize;

class Authorize extends BaseAuthorize
{
    protected function unauthorized($request, array $abilities)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        abort(403);
    }
}
