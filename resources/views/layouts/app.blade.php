<!DOCTYPE html>
<html lang="{{ $currentLocale ?? 'he' }}" dir="rtl" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#17C3B2">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>@yield('title', \App\Models\Setting::get('app_name', 'ForgeDesk Studio'))</title>
    @php
        $logo = \App\Models\Setting::get('app_logo');
    @endphp
    @if($logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($logo))
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $logo) }}">
        <link rel="apple-touch-icon" href="{{ asset('storage/' . $logo) }}">
    @else
        <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='8' fill='%2317C3B2'/><text x='50%25' y='70%25' text-anchor='middle' fill='white' font-size='20' font-weight='bold' font-family='sans-serif'>F</text></svg>">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@300;400;500;600;700;800&family=Open+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/app.css">
    <script>
        (function() {
            var saved = localStorage.getItem('theme');
            if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
            if (localStorage.getItem('sidebar-collapsed') === 'true') {
                document.documentElement.classList.add('sidebar-was-collapsed');
            }
        })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@0.460.0/dist/umd/lucide.min.js" integrity="sha384-ieG+IKD0d/ZPXyCBTMVAbqsQdns8QGJR/e26WMw7M4fkaI/rHcS/YIoi+ah9WGge" crossorigin="anonymous"></script>
    <script>
        tailwind.config = {
            darkMode: ['selector', '[data-theme="dark"]'],
            theme: {
                extend: {
                    fontFamily: { sans: ['Open Sans', 'Noto Sans Arabic', 'system-ui', 'sans-serif'] },
                    colors: {
                        accent: { DEFAULT:'#17C3B2', light:'#E6F9F7', dark:'#13A89A' },
                        surface: '#F7F9FC',
                        border: '#E8EDF3',
                        ink: { DEFAULT:'#1E293B', secondary:'#64748B', muted:'#94A3B8' },
                        success: '#22C55E',
                        warning: '#F59E0B',
                        danger: '#EF4444',
                    },
                    borderRadius: { card:'20px', btn:'12px', input:'12px', badge:'999px' },
                    boxShadow: {
                        card: '0 1px 2px rgba(15,23,42,.04), 0 4px 12px rgba(15,23,42,.03)',
                        elevated: '0 4px 16px rgba(15,23,42,.06)',
                        soft: '0 1px 3px rgba(15,23,42,.05)',
                    }
                }
            }
        }
    </script>
    @stack('styles')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js" integrity="sha384-9Ax3MmS9AClxJyd5/zafcXXjxmwFhZCdsT6HJoJjarvCaAkJlk5QDzjLJm+Wdx5F" crossorigin="anonymous"></script>
</head>
<body class="min-h-screen font-sans transition-colors duration-200" style="background:var(--color-surface); color:var(--color-ink);">
    @auth
    <div class="min-h-screen flex">
        <x-sidebar />

        <div id="sidebar-overlay" class="fixed inset-0 bg-black/30 z-40 hidden backdrop-blur-sm" onclick="toggleSidebar()"></div>

        <div id="main-content" class="flex-1 flex flex-col min-h-screen">
            <header class="border-b h-16 flex items-center justify-between px-6 lg:px-8 sticky top-0 z-30 transition-colors" style="background:var(--color-header-bg); border-color:var(--color-border); backdrop-filter:blur(12px); -webkit-backdrop-filter:blur(12px);">
                <div class="flex items-center gap-3">
                    <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-btn text-ink-secondary transition" style="color:var(--color-ink-secondary);">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>
                    @hasSection('back')
                        @yield('back')
                    @endif
                    @hasSection('breadcrumbs')
                        @yield('breadcrumbs')
                    @else
                        <h1 class="text-lg font-semibold" style="color:var(--color-ink);">@yield('header', \App\Models\Setting::get('app_name', 'ForgeDesk Studio'))</h1>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    @yield('actions')
                    @auth
                    <div x-data="{ langOpen: false }" class="relative hidden sm:block">
                        <button @click="langOpen = !langOpen" @keydown.escape="langOpen = false"
                                class="flex items-center gap-1.5 px-2 sm:px-3 py-2.5 rounded-btn text-sm font-medium transition border"
                                style="color:var(--color-ink-secondary); border-color:var(--color-border); background:var(--color-bg);">
                            <i data-lucide="globe" class="w-4 h-4"></i>
                            <span class="hidden sm:inline">{{ $currentLocale === 'ar' ? __('lang_short_ar') : __('lang_short_he') }}</span>
                            <i data-lucide="chevron-down" class="hidden sm:block w-3 h-3"></i>
                        </button>
                        <div x-show="langOpen" @click.away="langOpen = false" x-transition x-cloak
                             class="absolute top-full mt-2 start-0 w-40 rounded-card py-1 z-50 shadow-elevated"
                             style="background:var(--color-card); border:1px solid var(--color-border);">
                            <a href="{{ route('language.switch', 'he') }}"
                               class="flex items-center gap-2 px-4 py-2.5 text-sm transition"
                               style="color:{{ $currentLocale === 'he' ? 'var(--color-accent)' : 'var(--color-ink)' }};">
                                <i data-lucide="globe" class="w-4 h-4 flex-shrink-0" style="color:var(--color-ink-muted)"></i>
                                <span class="font-medium">{{ __('lang_hebrew') }}</span>
                                @if($currentLocale === 'he')<i data-lucide="check" class="w-4 h-4 ms-auto" style="color:var(--color-accent)"></i>@endif
                            </a>
                            <a href="{{ route('language.switch', 'ar') }}"
                               class="flex items-center gap-2 px-4 py-2.5 text-sm transition"
                               style="color:{{ $currentLocale === 'ar' ? 'var(--color-accent)' : 'var(--color-ink)' }};">
                                <i data-lucide="globe" class="w-4 h-4 flex-shrink-0" style="color:var(--color-ink-muted)"></i>
                                <span class="font-medium">{{ __('lang_arabic') }}</span>
                                @if($currentLocale === 'ar')<i data-lucide="check" class="w-4 h-4 ms-auto" style="color:var(--color-accent)"></i>@endif
                            </a>
                        </div>
                    </div>
                    @endauth
                </div>
            </header>

            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @if(session('success'))
                    <div class="border px-5 py-3.5 rounded-card mb-6 flex items-center gap-3 text-sm font-medium" style="background:var(--color-accent-light); border-color:rgba(23,195,178,.2); color:var(--color-accent-dark);">
                        <i data-lucide="circle-check" class="w-5 h-5 flex-shrink-0"></i>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="border px-5 py-3.5 rounded-card mb-6 flex items-center gap-3 text-sm font-medium" style="background:rgba(239,68,68,.08); border-color:rgba(239,68,68,.2); color:var(--color-danger);">
                        <i data-lucide="triangle-alert" class="w-5 h-5 flex-shrink-0"></i>
                        {{ session('error') }}
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    @else
        @yield('content')
    @endauth

    <script>
        function toggleTheme() {
            var html = document.documentElement;
            var current = html.getAttribute('data-theme');
            var next = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
        }

        function toggleSidebar() {
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('sidebar-overlay');
            var isOpen = sidebar.classList.contains('sidebar-open');
            if (isOpen) {
                sidebar.classList.remove('sidebar-open');
                overlay.classList.add('hidden');
            } else {
                sidebar.classList.add('sidebar-open');
                overlay.classList.remove('hidden');
            }
        }

        function toggleSidebarCollapse() {
            var sidebar = document.getElementById('sidebar');
            var main = document.getElementById('main-content');
            var isCollapsed = sidebar.classList.toggle('collapsed');
            main.classList.toggle('sidebar-collapsed', isCollapsed);
            localStorage.setItem('sidebar-collapsed', isCollapsed);
        }

        document.addEventListener('DOMContentLoaded', function() {
            var sidebar = document.getElementById('sidebar');
            var main = document.getElementById('main-content');
            if (window.innerWidth >= 1024 && localStorage.getItem('sidebar-collapsed') === 'true') {
                sidebar.classList.add('collapsed');
                main.classList.add('sidebar-collapsed');
                document.documentElement.classList.remove('sidebar-was-collapsed');
            }
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    </script>
    @stack('scripts')
</body>
</html>
