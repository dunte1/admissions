<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleLanguage
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->get('lang') ?? session('locale', 'en');
        
        if (in_array($locale, ['en', 'sw'])) {
            app()->setLocale($locale);
            session(['locale' => $locale]);
        }

        return $next($request);
    }
}
