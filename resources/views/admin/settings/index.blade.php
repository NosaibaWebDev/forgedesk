@extends('layouts.app')

@section('title', __('settings_title'))
@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm">
    <a href="{{ route('admin.dashboard') }}" class="text-ink-muted hover:text-accent transition">{{ __('home') }}</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <span class="text-ink font-medium">{{ __('settings') }}</span>
</nav>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    {{-- App Settings --}}
    <div class="bg-white rounded-card border border-border shadow-card p-6 mb-6">
        <h3 class="text-lg font-semibold text-ink mb-4">{{ __('app_settings') }}</h3>
        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label">{{ __('app_name') }}</label>
                    <input type="text" name="app_name" value="{{ $settings['app_name'] }}" required
                        class="input">
                </div>
                <div>
                    <label class="label">{{ __('app_tagline') }}</label>
                    <input type="text" name="app_tagline" value="{{ $settings['app_tagline'] }}"
                        class="input">
                </div>
                <div class="md:col-span-2">
                    <label class="label">{{ __('app_logo') }}</label>
                    <div class="flex items-center gap-4">
                        @if($settings['app_logo'])
                            <div class="flex items-center gap-3">
                                <img src="{{ Storage::disk('public')->url($settings['app_logo']) }}" alt="{{ __('logo_alt') }}" width="64" height="64" class="h-16 w-16 object-contain rounded-card border border-border">
                                <button type="button" onclick="deleteLogo()" class="btn-danger btn-sm">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    {{ __('delete_logo') }}
                                </button>
                            </div>
                        @endif
                        <div class="flex-1">
                            <input type="file" name="app_logo" accept="image/*"
                                class="file-input">
                            <p class="text-xs text-ink-muted mt-1">{{ __('logo_helper') }}</p>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="label">{{ __('currency') }}</label>
                    <select name="currency" class="input">
                        <option value="₪" {{ $settings['currency'] === '₪' ? 'selected' : '' }}>{{ __('currency_ils') }}</option>
                        <option value="$" {{ $settings['currency'] === '$' ? 'selected' : '' }}>{{ __('currency_usd') }}</option>
                        <option value="€" {{ $settings['currency'] === '€' ? 'selected' : '' }}>{{ __('currency_eur') }}</option>
                    </select>
                </div>
                <div>
                    <label class="label">{{ __('timezone') }}</label>
                    <select name="timezone" class="input">
                        <option value="Asia/Jerusalem" {{ $settings['timezone'] === 'Asia/Jerusalem' ? 'selected' : '' }}>Asia/Jerusalem</option>
                        <option value="UTC" {{ $settings['timezone'] === 'UTC' ? 'selected' : '' }}>UTC</option>
                        <option value="Europe/London" {{ $settings['timezone'] === 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
                        <option value="America/New_York" {{ $settings['timezone'] === 'America/New_York' ? 'selected' : '' }}>America/New_York</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end mt-6">
                <button type="submit" class="btn-primary">
                    {{ __('save_settings') }}
                </button>
            </div>
        </form>
    </div>

    {{-- Company Settings --}}
    <div class="bg-white rounded-card border border-border shadow-card p-6 mb-6">
        <h3 class="text-lg font-semibold text-ink mb-4">{{ __('company_details') }}</h3>
        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label">{{ __('company_name') }}</label>
                    <input type="text" name="company_name" value="{{ $settings['company_name'] }}"
                        class="input">
                </div>
                <div>
                    <label class="label">{{ __('company_email') }}</label>
                    <input type="email" name="company_email" value="{{ $settings['company_email'] }}"
                        class="input">
                </div>
                <div>
                    <label class="label">{{ __('company_phone') }}</label>
                    <input type="text" name="company_phone" value="{{ $settings['company_phone'] }}"
                        class="input">
                </div>
                <div>
                    <label class="label">{{ __('address') }}</label>
                    <input type="text" name="company_address" value="{{ $settings['company_address'] }}"
                        class="input">
                </div>
            </div>
            <div class="flex justify-end mt-6">
                <button type="submit" class="btn-primary">
                    {{ __('save_company') }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function deleteLogo() {
        if (!confirm('{{ __("confirm_delete_logo") }}')) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.settings.clear-logo") }}';
        form.innerHTML = '@csrf<input type="hidden" name="_method" value="DELETE">';
        document.body.appendChild(form);
        form.submit();
    }
</script>
@endpush
@endsection