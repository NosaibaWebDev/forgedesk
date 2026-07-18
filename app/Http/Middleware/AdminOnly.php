<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()->isAdmin()) {
            return redirect()->route('login')->with('error', 'אין לך גישה לאזור זה.');
        }

        if (!$request->user()->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'החשבון שלך הושבת. אנא פנה למנהל.');
        }

        return $next($request);
    }
}
