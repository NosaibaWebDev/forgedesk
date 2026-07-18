@extends('layouts.app')

@section('title', __('new_client_title'))
@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm">
    <a href="{{ route('admin.dashboard') }}" class="text-ink-muted hover:text-accent transition">{{ __('home') }}</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <a href="{{ route('admin.clients.index') }}" class="text-ink-muted hover:text-accent transition">{{ __('clients') }}</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <span class="text-ink font-medium">{{ __('new_client') }}</span>
</nav>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-card border border-border shadow-card p-4 sm:p-6">
        <form method="POST" action="{{ route('admin.clients.store') }}">
            @csrf

            <div class="mb-4">
                <label for="name" class="label">{{ __('name') }} *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                    class="input" placeholder="{{ __('enter_client_name') }}">
                @error('name') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="label">{{ __('email') }} *</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                    class="input" placeholder="{{ __('enter_client_email') }}">
                @error('email') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="phone" class="label">{{ __('phone') }}</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                    class="input" placeholder="{{ __('enter_client_phone') }}">
                @error('phone') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="company" class="label">{{ __('company') }}</label>
                <input type="text" id="company" name="company" value="{{ old('company') }}"
                    class="input" placeholder="{{ __('enter_client_company') }}">
                @error('company') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="address" class="label">{{ __('address') }}</label>
                <input type="text" id="address" name="address" value="{{ old('address') }}"
                    class="input" placeholder="{{ __('enter_client_address') }}">
                @error('address') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="label">{{ __('password') }} *</label>
                <input type="password" id="password" name="password" required
                    class="input" placeholder="{{ __('enter_password') }}">
                @error('password') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="label">{{ __('confirm_password') }} *</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                    class="input" placeholder="{{ __('enter_password_again') }}">
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="btn-primary">
                    {{ __('create_client') }}
                </button>
                <a href="{{ route('admin.clients.index') }}" class="btn-ghost">
                    {{ __('cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection