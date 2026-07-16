<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::getMany([
            'app_name' => 'ForgeDesk Studio',
            'app_tagline' => 'מערכת ניהול פרילנסרים',
            'app_logo' => '',
            'company_name' => '',
            'company_email' => '',
            'company_phone' => '',
            'company_address' => '',
            'currency' => '₪',
            'date_format' => 'd/m/Y',
            'timezone' => 'Asia/Jerusalem',
        ]);

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:100',
            'app_tagline' => 'nullable|string|max:255',
            'app_logo' => 'nullable|image|max:2048|mimes:jpg,jpeg,png,gif,webp',
            'company_name' => 'nullable|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'company_phone' => 'nullable|string|max:50',
            'company_address' => 'nullable|string|max:500',
            'currency' => 'nullable|string|max:5',
            'date_format' => 'nullable|string|max:20',
            'timezone' => 'nullable|string|max:50',
        ]);

        if ($request->hasFile('app_logo')) {
            $oldLogo = Setting::get('app_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            $file = $request->file('app_logo');
            $storedName = 'logos/' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('', $storedName, 'public');
            $validated['app_logo'] = $storedName;
        }

        foreach ($validated as $key => $value) {
            if ($key === 'app_logo' && $value === null) {
                continue;
            }
            Setting::set($key, $value);
        }

        return redirect()->route('admin.settings.index')->with('success', 'ההגדרות נשמרו בהצלחה.');
    }

    public function clearLogo()
    {
        $oldLogo = Setting::get('app_logo');
        if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
            Storage::disk('public')->delete($oldLogo);
        }
        Setting::set('app_logo', '');

        return redirect()->route('admin.settings.index')->with('success', 'הלוגו הוסר בהצלחה.');
    }
}
