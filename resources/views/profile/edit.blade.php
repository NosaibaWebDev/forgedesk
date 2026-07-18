@extends('layouts.app')

@section('title', __('profile_title'))
@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm">
    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('client.dashboard') }}" class="text-ink-muted hover:text-accent transition">{{ __('home') }}</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <span class="text-ink font-medium">{{ __('profile') }}</span>
</nav>
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    {{-- Avatar --}}
    <div class="bg-white rounded-card border border-border shadow-card p-6">
        <h2 class="text-lg font-semibold text-ink mb-5">{{ __('profile_picture') }}</h2>
        <div class="flex items-center gap-6">
            <div class="flex flex-col items-center gap-2">
                <div x-data="{ preview: '{{ $user->avatar && Storage::disk('public')->exists($user->avatar) ? Storage::disk('public')->url($user->avatar) : '' }}' }">
                    <template x-if="preview">
                                                        <img :src="preview" alt="{{ __('avatar') }}" width="80" height="80" class="w-20 h-20 rounded-full object-cover border-2 border-border">
                    </template>
                    <template x-if="!preview">
                        <div class="w-20 h-20 rounded-full bg-accent/10 flex items-center justify-center text-accent border-2 border-border">
                            <i data-lucide="user" class="w-10 h-10"></i>
                        </div>
                    </template>
                </div>
                @if($user->avatar)
                    <form method="POST" action="{{ route('profile.avatar.destroy') }}" onsubmit="return confirm('{{ __("confirm") }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-danger hover:text-red-600 font-medium transition">{{ __('remove_image') }}</button>
                    </form>
                @else
                    <span class="text-xs text-ink-muted">{{ __('no_image') }}</span>
                @endif
            </div>
            <div class="flex-1">
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="avatar-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="name" value="{{ $user->name }}">
                    <input type="hidden" name="email" value="{{ $user->email }}">
                    <input type="hidden" name="preferred_language" value="{{ $user->preferred_language }}">
                    <div class="flex items-end gap-3">
                        <label class="flex-1">
                            <span class="label">{{ __('choose_image') }}</span>
                            <input type="file" name="avatar" accept="image/*" class="file-input">
                        </label>
                        <button type="submit" class="btn-primary flex-shrink-0">
                            {{ __('upload') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Personal Info --}}
    <div class="bg-white rounded-card border border-border shadow-card p-6">
        <h2 class="text-lg font-semibold text-ink mb-5">{{ __('personal_details') }}</h2>
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="label">{{ __('name') }}</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="input" dir="rtl">
                    @error('name')<p class="text-danger text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label">{{ __('email') }}</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="input" dir="ltr">
                    @error('email')<p class="text-danger text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label">{{ __('phone') }}</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="input" dir="ltr">
                    @error('phone')<p class="text-danger text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label">{{ __('company') }}</label>
                    <input type="text" name="company" value="{{ old('company', $user->company) }}" class="input" dir="rtl">
                    @error('company')<p class="text-danger text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label">{{ __('language') }}</label>
                    <select name="preferred_language" class="input">
                        <option value="he" {{ old('preferred_language', $user->preferred_language) === 'he' ? 'selected' : '' }}>🇮🇱 עברית</option>
                        <option value="ar" {{ old('preferred_language', $user->preferred_language) === 'ar' ? 'selected' : '' }}>🇸🇦 العربية</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary">{{ __('save') }}</button>
            </div>
        </form>
    </div>

    {{-- Password --}}
    <div class="bg-white rounded-card border border-border shadow-card p-6">
        <h2 class="text-lg font-semibold text-ink mb-5">{{ __('change_password') }}</h2>
        <form method="POST" action="{{ route('profile.password.update') }}">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="label">{{ __('current_password') }}</label>
                    <input type="password" name="current_password" required class="input" dir="ltr">
                    @error('current_password')<p class="text-danger text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label">{{ __('new_password') }}</label>
                    <input type="password" name="password" required class="input" dir="ltr">
                    @error('password')<p class="text-danger text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label">{{ __('confirm_password_label') }}</label>
                    <input type="password" name="password_confirmation" required class="input" dir="ltr">
                </div>
                <button type="submit" class="btn-primary">{{ __('update_password') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
