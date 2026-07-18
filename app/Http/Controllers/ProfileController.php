<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        if ($request->hasFile('avatar')) {
            try {
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
            } catch (\Exception $e) {
                return back()->withInput()->with('error', __('avatar_upload_failed'));
            }
        }

        $user->forceFill($validated)->save();

        return redirect()->route('profile.edit')->with('success', __('profile_updated'));
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        Auth::user()->update([
            'password' => $request->password,
        ]);

        return redirect()->route('profile.edit')->with('success', __('password_updated'));
    }

    public function destroyAvatar()
    {
        $user = Auth::user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
        }

        return redirect()->route('profile.edit')->with('success', __('image_removed'));
    }
}
