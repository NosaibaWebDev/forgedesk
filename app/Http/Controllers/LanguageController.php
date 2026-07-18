<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class LanguageController extends Controller
{
    public function switch(Request $request, string $locale)
    {
        if (!in_array($locale, ['he', 'ar'])) {
            abort(400);
        }

        if (Auth::check()) {
            Auth::user()->update(['preferred_language' => $locale]);
        } else {
            session(['preferred_language' => $locale]);
        }

        app()->setLocale($locale);

        return redirect()->back()->with('success', $locale === 'he' ? 'שפה שונתה לעברית' : 'تم تغيير اللغة إلى العربية');
    }
}
