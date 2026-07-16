@extends('layouts.app')

@section('title', 'הגדרות - מנהל')
@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm">
    <a href="{{ route('admin.dashboard') }}" class="text-ink-muted hover:text-accent transition">בית</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <span class="text-ink font-medium">הגדרות</span>
</nav>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    {{-- App Settings --}}
    <div class="bg-white rounded-card border border-border shadow-card p-6 mb-6">
        <h3 class="text-lg font-semibold text-ink mb-4">הגדרות אפליקציה</h3>
        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label">שם האפליקציה</label>
                    <input type="text" name="app_name" value="{{ $settings['app_name'] }}" required
                        class="input">
                </div>
                <div>
                    <label class="label">תיאור קצר</label>
                    <input type="text" name="app_tagline" value="{{ $settings['app_tagline'] }}"
                        class="input">
                </div>
                <div class="md:col-span-2">
                    <label class="label">לוגו האפליקציה</label>
                    <div class="flex items-center gap-4">
                        @if($settings['app_logo'])
                            <div class="flex items-center gap-3">
                                <img src="{{ Storage::disk('public')->url($settings['app_logo']) }}" alt="לוגו" width="64" height="64" class="h-16 w-16 object-contain rounded-card border border-border">
                                <button type="button" onclick="deleteLogo()" class="btn-danger btn-sm">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    מחק לוגו
                                </button>
                            </div>
                        @endif
                        <div class="flex-1">
                            <input type="file" name="app_logo" accept="image/*"
                                class="file-input">
                            <p class="text-xs text-ink-muted mt-1">PNG, JPG, GIF, WEBP או SVG. מקסימום 2MB.</p>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="label">מטבע</label>
                    <select name="currency" class="input">
                        <option value="₪" {{ $settings['currency'] === '₪' ? 'selected' : '' }}>₪ שקל</option>
                        <option value="$" {{ $settings['currency'] === '$' ? 'selected' : '' }}>$ דולר</option>
                        <option value="€" {{ $settings['currency'] === '€' ? 'selected' : '' }}>€ יורו</option>
                    </select>
                </div>
                <div>
                    <label class="label">אזור זמן</label>
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
                    שמור הגדרות
                </button>
            </div>
        </form>
    </div>

    {{-- Company Settings --}}
    <div class="bg-white rounded-card border border-border shadow-card p-6 mb-6">
        <h3 class="text-lg font-semibold text-ink mb-4">פרטי חברה</h3>
        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label">שם החברה</label>
                    <input type="text" name="company_name" value="{{ $settings['company_name'] }}"
                        class="input">
                </div>
                <div>
                    <label class="label">דוא"ל חברה</label>
                    <input type="email" name="company_email" value="{{ $settings['company_email'] }}"
                        class="input">
                </div>
                <div>
                    <label class="label">טלפון חברה</label>
                    <input type="text" name="company_phone" value="{{ $settings['company_phone'] }}"
                        class="input">
                </div>
                <div>
                    <label class="label">כתובת</label>
                    <input type="text" name="company_address" value="{{ $settings['company_address'] }}"
                        class="input">
                </div>
            </div>
            <div class="flex justify-end mt-6">
                <button type="submit" class="btn-primary">
                    שמור פרטי חברה
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function deleteLogo() {
        if (!confirm('האם אתה בטוח שברצונך למחוק את הלוגו?')) return;
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
