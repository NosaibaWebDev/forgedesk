<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="rtl" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - {{ \App\Models\Setting::get('app_name', 'ForgeDesk Studio') }}</title>
    <link rel="stylesheet" href="/css/app.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: ['selector', '[data-theme="dark"]'],
            theme: { extend: { colors: { accent: { DEFAULT: '#17C3B2', dark: '#12A899', light: '#E8FAF8' }, surface: '#F7F9FC', border: '#E5E7EB', ink: { DEFAULT: '#111827', secondary: '#4B5563', muted: '#9CA3AF' } }, borderRadius: { card: '20px', btn: '12px' } } }
        }
    </script>
</head>
<body class="min-h-screen flex items-center justify-center font-sans antialiased" style="background:var(--color-surface); color:var(--color-ink);">
    <div class="text-center px-4">
        <div class="w-20 h-20 rounded-card flex items-center justify-center mx-auto mb-6" style="background:rgba(245,158,11,.1);">
            <span class="text-4xl font-bold text-amber-500">500</span>
        </div>
        <h1 class="text-3xl font-bold mb-2" style="color:var(--color-ink);">{{ __('server_error') }}</h1>
        <p class="text-lg mb-8" style="color:var(--color-ink-secondary);">{{ __('server_error_desc') }}</p>
        <a href="/" class="inline-flex items-center gap-2 px-6 py-3 rounded-btn font-medium transition" style="background:var(--color-accent); color:white;">
            {{ __('back_to_home') }}
        </a>
    </div>
</body>
</html>
