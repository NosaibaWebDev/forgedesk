<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = 'he';

        if (Auth::check()) {
            $locale = Auth::user()->preferred_language ?? 'he';
        } elseif ($request->has('lang')) {
            $locale = in_array($request->query('lang'), ['he', 'ar']) ? $request->query('lang') : 'he';
            session(['preferred_language' => $locale]);
        } elseif (session('preferred_language')) {
            $locale = session('preferred_language');
        }

        App::setLocale($locale);
        view()->share('currentLocale', $locale);

        return $next($request);
    }
}
