<!DOCTYPE html>
<html lang="he" dir="rtl" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#17C3B2">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="/manifest.json">
    <title>@yield('title', 'ForgeDesk Studio')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: ['selector', '[data-theme="dark"]'],
            theme: {
                extend: {
                    fontFamily: { sans: ['Open Sans', 'system-ui', 'sans-serif'] },
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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
                        <h1 class="text-lg font-semibold" style="color:var(--color-ink);">@yield('header', 'ForgeDesk Studio')</h1>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    @yield('actions')
                </div>
            </header>

            <main class="flex-1 p-6 lg:p-8">
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
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(() => {});
        }
    </script>
    @stack('scripts')
</body>
</html>
