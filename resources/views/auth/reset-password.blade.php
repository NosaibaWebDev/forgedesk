<!DOCTYPE html>
<html lang="{{ $currentLocale ?? 'he' }}" dir="rtl" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('reset_password') }} - {{ \App\Models\Setting::get('app_name', 'ForgeDesk Studio') }}</title>
    @php $logo = \App\Models\Setting::get('app_logo'); @endphp
    @if($logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($logo))
        <link rel="icon" type="image/png" href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($logo) }}">
    @endif
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='8' fill='%2317C3B2'/><text x='50%25' y='70%25' text-anchor='middle' fill='white' font-size='20' font-weight='bold' font-family='sans-serif'>F</text></svg>"
    <link rel="stylesheet" href="/css/app.css">
    <script>
        (function() {
            var saved = localStorage.getItem('theme');
            if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: ['selector', '[data-theme="dark"]'],
            theme: {
                extend: {
                    colors: {
                        accent: { DEFAULT: '#17C3B2', dark: '#12A899', light: '#E8FAF8' },
                        surface: '#F7F9FC',
                        border: '#E5E7EB',
                        ink: { DEFAULT: '#111827', secondary: '#4B5563', muted: '#9CA3AF' },
                        danger: '#EF4444',
                    },
                    borderRadius: { card: '20px', btn: '12px', input: '12px', badge: '9999px' },
                    boxShadow: { card: '0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04)' },
                }
            }
        }
    </script>
</head>
<body class="min-h-screen flex items-center justify-center font-sans antialiased transition-colors" style="background:var(--color-surface); color:var(--color-ink);">
    <div class="w-full max-w-md px-4">
        <div class="rounded-card border shadow-card p-8 transition-colors" style="background:var(--color-card); border-color:var(--color-border);">
            <div class="text-center mb-8">
                @if($logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($logo))
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($logo) }}" alt="Logo" class="w-16 h-16 object-contain mx-auto mb-4">
                @else
                    <div class="w-12 h-12 rounded-card flex items-center justify-center mx-auto mb-4" style="background:var(--color-accent-light);">
                        <i data-lucide="hexagon" class="w-6 h-6 text-accent"></i>
                    </div>
                @endif
                <h1 class="text-2xl font-bold" style="color:var(--color-ink);">{{ \App\Models\Setting::get('app_name', 'ForgeDesk Studio') }}</h1>
                <p class="mt-1.5 text-sm" style="color:var(--color-ink-secondary);">{{ __('reset_password_title') }}</p>
            </div>

            @if($errors->any())
                <div class="border px-4 py-3 rounded-input mb-6" style="background:rgba(239,68,68,.08); border-color:rgba(239,68,68,.2); color:var(--color-danger);">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="text-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-5">
                    <label for="email" class="label">{{ __('email') }}</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $email ?? '') }}"
                        required
                        autofocus
                        class="input text-right"
                        placeholder="example@email.com"
                    >
                </div>

                <div class="mb-5">
                    <label for="password" class="label">{{ __('new_password') }}</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="input text-right"
                        placeholder="{{ __('enter_password') }}"
                    >
                </div>

                <div class="mb-6">
                    <label for="password_confirmation" class="label">{{ __('confirm_password_label') }}</label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        required
                        class="input text-right"
                        placeholder="{{ __('enter_password_again') }}"
                    >
                </div>

                <button
                    type="submit"
                    class="w-full btn-primary justify-center"
                >
                    {{ __('reset_password_button') }}
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-sm font-medium transition" style="color:var(--color-accent);">
                    {{ __('back_to_login') }}
                </a>
            </div>

            <div class="mt-4 flex justify-center gap-4">
                <button onclick="toggleTheme()" class="flex items-center gap-2 text-sm transition" style="color:var(--color-ink-muted);">
                    <i data-lucide="moon" class="w-4 h-4 dark:hidden"></i>
                    <i data-lucide="sun" class="w-4 h-4 hidden dark:block"></i>
                    <span class="dark:hidden">{{ __('dark_mode') }}</span>
                    <span class="hidden dark:block">{{ __('light_mode') }}</span>
                </button>
                <a href="{{ route('language.switch', $currentLocale === 'he' ? 'ar' : 'he') }}" class="flex items-center gap-2 text-sm transition" style="color:var(--color-ink-muted);">
                    <i data-lucide="globe" class="w-4 h-4"></i>
                    <span>{{ $currentLocale === 'ar' ? __('lang_short_ar') : __('lang_short_he') }}</span>
                </a>
            </div>
        </div>

        <p class="text-center text-sm mt-6" style="color:var(--color-ink-muted);">
            &copy; {{ date('Y') }} {{ \App\Models\Setting::get('app_name', 'ForgeDesk Studio') }}. {{ __('copyright') }}
        </p>
    </div>

    <script src="https://unpkg.com/lucide@0.460.0/dist/umd/lucide.min.js" integrity="sha384-ieG+IKD0d/ZPXyCBTMVAbqsQdns8QGJR/e26WMw7M4fkaI/rHcS/YIoi+ah9WGge" crossorigin="anonymous"></script>
    <script>
        function toggleTheme() {
            var html = document.documentElement;
            var current = html.getAttribute('data-theme');
            var next = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
        }
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    </script>
</body>
</html>
