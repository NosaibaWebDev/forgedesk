@php
    $appLogo = \App\Models\Setting::get('app_logo');
    $appName = \App\Models\Setting::get('app_name', 'ForgeDesk');
@endphp

<aside id="sidebar" class="fixed inset-y-0 right-0 z-50 flex flex-col transition-colors" style="background:var(--color-bg); border-left:1px solid var(--color-border);">

    <div class="h-16 flex items-center px-5 border-b flex-shrink-0 transition-colors" style="border-color:var(--color-border);">
        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('client.dashboard') }}" class="flex items-center gap-2.5">
            @if($appLogo && \Illuminate\Support\Facades\Storage::disk('public')->exists($appLogo))
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($appLogo) }}" alt="Logo" width="32" height="32" class="h-8 w-8 object-contain rounded flex-shrink-0">
            @else
                <div class="w-8 h-8 bg-accent rounded-btn flex items-center justify-center text-white font-bold text-sm flex-shrink-0">F</div>
            @endif
            <span class="sidebar-brand-text text-[15px] font-bold" style="color:var(--color-ink);">{{ $appName }}</span>
        </a>
    </div>

    <nav class="flex-1 mt-5 px-3 space-y-0.5 overflow-y-auto">
        @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-btn text-sm transition {{ request()->routeIs('admin.dashboard') ? 'nav-item-active' : '' }}" style="color:var(--color-ink-secondary);">
                <i data-lucide="layout-dashboard" class="w-5 h-5 flex-shrink-0"></i>
                <span class="nav-label">{{ __('dashboard') }}</span>
            </a>
            <a href="{{ route('admin.projects.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-btn text-sm transition {{ request()->routeIs('admin.projects.*') ? 'nav-item-active' : '' }}" style="color:var(--color-ink-secondary);">
                <i data-lucide="folder-kanban" class="w-5 h-5 flex-shrink-0"></i>
                <span class="nav-label">{{ __('projects') }}</span>
            </a>
            <a href="{{ route('admin.clients.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-btn text-sm transition {{ request()->routeIs('admin.clients.*') ? 'nav-item-active' : '' }}" style="color:var(--color-ink-secondary);">
                <i data-lucide="users" class="w-5 h-5 flex-shrink-0"></i>
                <span class="nav-label">{{ __('clients') }}</span>
            </a>
            <a href="{{ route('admin.messages.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-btn text-sm transition {{ request()->routeIs('admin.messages.*') ? 'nav-item-active' : '' }}" style="color:var(--color-ink-secondary);">
                <i data-lucide="message-square" class="w-5 h-5 flex-shrink-0"></i>
                <span class="nav-label">{{ __('messages') }}</span>
                @php $unread = auth()->user()->unreadMessagesCount(); @endphp
                @if($unread > 0)
                    <span class="nav-badge mr-auto bg-danger text-white text-[11px] font-medium rounded-badge px-2 py-0.5 leading-none">{{ $unread }}</span>
                @endif
            </a>
            <a href="{{ route('admin.timetracker.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-btn text-sm transition {{ request()->routeIs('admin.timetracker.*') ? 'nav-item-active' : '' }}" style="color:var(--color-ink-secondary);">
                <i data-lucide="clock" class="w-5 h-5 flex-shrink-0"></i>
                <span class="nav-label">{{ __('time_tracking') }}</span>
            </a>
            <div class="my-3 mx-2 border-b" style="border-color:var(--color-border);"></div>
            <a href="{{ route('admin.settings.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-btn text-sm transition {{ request()->routeIs('admin.settings.*') ? 'nav-item-active' : '' }}" style="color:var(--color-ink-secondary);">
                <i data-lucide="settings" class="w-5 h-5 flex-shrink-0"></i>
                <span class="nav-label">{{ __('settings') }}</span>
            </a>
        @else
            <a href="{{ route('client.dashboard') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-btn text-sm transition {{ request()->routeIs('client.dashboard') ? 'nav-item-active' : '' }}" style="color:var(--color-ink-secondary);">
                <i data-lucide="layout-dashboard" class="w-5 h-5 flex-shrink-0"></i>
                <span class="nav-label">{{ __('dashboard') }}</span>
            </a>
            <a href="{{ route('client.projects.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-btn text-sm transition {{ request()->routeIs('client.projects.*') ? 'nav-item-active' : '' }}" style="color:var(--color-ink-secondary);">
                <i data-lucide="folder-kanban" class="w-5 h-5 flex-shrink-0"></i>
                <span class="nav-label">{{ __('my_projects') }}</span>
            </a>
            <a href="{{ route('client.messages.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-btn text-sm transition {{ request()->routeIs('client.messages.*') ? 'nav-item-active' : '' }}" style="color:var(--color-ink-secondary);">
                <i data-lucide="message-square" class="w-5 h-5 flex-shrink-0"></i>
                <span class="nav-label">{{ __('messages') }}</span>
                @php $unread = auth()->user()->unreadMessagesCount(); @endphp
                @if($unread > 0)
                    <span class="nav-badge mr-auto bg-danger text-white text-[11px] font-medium rounded-badge px-2 py-0.5 leading-none">{{ $unread }}</span>
                @endif
            </a>
        @endif
            <div class="my-3 mx-2 border-b" style="border-color:var(--color-border);"></div>
            <button onclick="event.stopPropagation(); toggleSidebarCollapse()" class="toggle-sidebar-btn nav-item w-full flex items-center gap-3 px-3 py-2.5 rounded-btn text-sm transition hidden lg:flex text-right" style="color:var(--color-ink-secondary);">
                <i data-lucide="panel-right-close" class="w-5 h-5 flex-shrink-0"></i>
                <span class="nav-label">{{ __('collapse_menu') }}</span>
            </button>
            <button onclick="event.stopPropagation(); toggleTheme()" class="nav-item w-full flex items-center gap-3 px-3 py-2.5 rounded-btn text-sm transition hidden lg:flex text-right" style="color:var(--color-ink-secondary);">
                <i data-lucide="moon" class="w-5 h-5 flex-shrink-0 dark:hidden"></i>
                <i data-lucide="sun" class="w-5 h-5 flex-shrink-0 hidden dark:block"></i>
                <span class="nav-label dark:hidden">{{ __('dark_mode') }}</span>
                <span class="nav-label hidden dark:block">{{ __('light_mode') }}</span>
            </button>
    </nav>

    <div class="border-t p-3 flex-shrink-0 transition-colors" style="border-color:var(--color-border);">
        <a href="{{ route('profile.edit') }}" class="sidebar-user-info-parent flex items-center gap-3 p-2 -m-2 rounded-btn transition" style="color:var(--color-ink-secondary);">
            <x-user-avatar :user="auth()->user()" size="sm" class="text-accent" />
            <div class="sidebar-user-info flex-1 min-w-0">
                <p class="text-sm font-medium truncate" style="color:var(--color-ink);">{{ auth()->user()->name }}</p>
                <p class="text-xs truncate" style="color:var(--color-ink-muted);">{{ auth()->user()->email }}</p>
            </div>
        </a>
        <form method="POST" action="{{ route('logout') }}" class="mt-2">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-btn text-sm transition" style="color:var(--color-ink-muted);">
                <i data-lucide="log-out" class="w-4 h-4 flex-shrink-0"></i>
                <span class="sidebar-logout-text">{{ __('logout') }}</span>
            </button>
        </form>
    </div>
</aside>
