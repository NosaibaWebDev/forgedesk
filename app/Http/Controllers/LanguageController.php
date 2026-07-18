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

        $fallback = '/';
        if (Auth::check()) {
            $fallback = Auth::user()->isAdmin() ? route('admin.dashboard') : route('client.dashboard');
        }

        return redirect()->to(url()->previous($fallback))->with('success', __('language_changed'));
    }
}
