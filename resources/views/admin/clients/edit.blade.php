@extends('layouts.app')

@section('title', __('edit_client'))
@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm">
    <a href="{{ route('admin.dashboard') }}" class="text-ink-muted hover:text-accent transition">{{ __('home') }}</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <a href="{{ route('admin.clients.index') }}" class="text-ink-muted hover:text-accent transition">{{ __('clients') }}</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <a href="{{ route('admin.clients.show', $client) }}" class="text-ink-muted hover:text-accent transition">{{ $client->name }}</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <span class="text-ink font-medium">{{ __('edit') }}</span>
</nav>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-card border border-border shadow-card p-6">
        <form method="POST" action="{{ route('admin.clients.update', $client) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="label">{{ __('name') }} *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $client->name) }}" required
                    class="input" placeholder="{{ __('enter_client_name') }}">
                @error('name') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="label">{{ __('email') }} *</label>
                <input type="email" id="email" name="email" value="{{ old('email', $client->email) }}" required
                    class="input" placeholder="{{ __('enter_client_email') }}">
                @error('email') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="phone" class="label">{{ __('phone') }}</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $client->phone) }}"
                    class="input" placeholder="{{ __('enter_client_phone') }}">
                @error('phone') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="company" class="label">{{ __('company') }}</label>
                <input type="text" id="company" name="company" value="{{ old('company', $client->company) }}"
                    class="input" placeholder="{{ __('enter_client_company') }}">
                @error('company') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="address" class="label">{{ __('address') }}</label>
                <input type="text" id="address" name="address" value="{{ old('address', $client->address) }}"
                    class="input" placeholder="{{ __('enter_client_address') }}">
                @error('address') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="mb-6">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $client->is_active) ? 'checked' : '' }}
                        class="w-4 h-4 text-accent border-border rounded focus:ring-accent/30">
                    <span class="text-sm font-medium text-ink">{{ __('client_active') }}</span>
                </label>
                    @error('is_active') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="btn-primary">
                    {{ __('update_client') }}
                </button>
                <a href="{{ route('admin.clients.show', $client) }}" class="btn-ghost">
                    {{ __('cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection