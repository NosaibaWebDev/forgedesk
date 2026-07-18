<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function showForm()
    {
        if (auth()->check()) {
            return auth()->user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('client.dashboard');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => __('val_name_required'),
            'name.string' => __('val_name_string'),
            'name.max' => __('val_name_max'),
            'email.required' => __('val_email_required'),
            'email.email' => __('val_email_format'),
            'email.unique' => __('val_email_unique'),
            'phone.string' => __('val_phone_string'),
            'phone.max' => __('val_phone_max'),
            'password.required' => __('val_password_required'),
            'password.string' => __('val_password_string'),
            'password.min' => __('val_password_min'),
            'password.confirmed' => __('val_password_confirmation'),
        ]);

        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            return back()->withInput($request->only('name', 'email', 'phone'))
                ->with('error', __('register_error'));
        }

        $user = new User();
        $user->forceFill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'client',
            'is_active' => true,
            'admin_id' => $admin->id,
            'email_verified_at' => now(),
        ]);
        $user->save();

        return redirect()->route('login')
            ->with('success', __('register_success'));
    }
}
