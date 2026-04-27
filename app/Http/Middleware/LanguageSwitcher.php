<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LanguageSwitcher
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->segment(1);
        
        if (in_array($locale, ['en', 'sw'])) {
            App::setLocale($locale);
            session(['locale' => $locale]);
        } elseif (session()->has('locale')) {
            App::setLocale(session('locale'));
        } else {
            App::setLocale('en');
        }

        return $next($request);
    }
}
