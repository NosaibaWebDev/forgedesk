@extends('layouts.app')

@section('title', 'לקוחות - מנהל')
@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm">
    <a href="{{ route('admin.dashboard') }}" class="text-ink-muted hover:text-accent transition">בית</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <span class="text-ink font-medium">לקוחות</span>
</nav>
@endsection

@section('actions')
<a href="{{ route('admin.clients.create') }}" class="btn-primary">
    + לקוח חדש
</a>
@endsection

@section('content')
<div class="bg-white rounded-card border border-border shadow-card overflow-hidden">
    <div class="px-6 py-5 border-b border-border">
        <h2 class="text-lg font-semibold text-ink">רשימת לקוחות</h2>
    </div>

    {{-- Desktop table --}}
    <div class="overflow-x-auto hidden md:block">
        <table class="w-full text-right">
            <thead>
                <tr class="border-b border-border">
                    <th class="px-6 py-4 text-xs font-semibold text-ink-muted uppercase tracking-wider">שם</th>
                    <th class="px-6 py-4 text-xs font-semibold text-ink-muted uppercase tracking-wider">דוא"ל</th>
                    <th class="px-6 py-4 text-xs font-semibold text-ink-muted uppercase tracking-wider">טלפון</th>
                    <th class="px-6 py-4 text-xs font-semibold text-ink-muted uppercase tracking-wider">חברה</th>
                    <th class="px-6 py-4 text-xs font-semibold text-ink-muted uppercase tracking-wider">פרויקטים</th>
                    <th class="px-6 py-4 text-xs font-semibold text-ink-muted uppercase tracking-wider">סטטוס</th>
                    <th class="px-6 py-4 text-xs font-semibold text-ink-muted uppercase tracking-wider">פעולה</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse($clients as $client)
                    <tr class="hover:bg-gray-50 transition cursor-pointer" onclick="window.location.href='{{ route('admin.clients.show', $client) }}'">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <x-user-avatar :user="$client" />
                                <span class="font-medium text-ink">{{ $client->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-ink-secondary">{{ $client->email }}</td>
                        <td class="px-6 py-4 text-ink-secondary">{{ $client->phone ?? '-' }}</td>
                        <td class="px-6 py-4 text-ink-secondary">{{ $client->company ?? '-' }}</td>
                        <td class="px-6 py-4 text-ink-secondary">{{ $client->projects_count }}</td>
                        <td class="px-6 py-4">
                            @if($client->is_active)
                                <span class="rounded-badge px-3 py-1 text-xs font-medium bg-green-100 text-green-800">פעיל</span>
                            @else
                                <span class="rounded-badge px-3 py-1 text-xs font-medium bg-red-100 text-red-800">מושבת</span>
                            @endif
                        </td>
                        <td class="px-6 py-4" onclick="event.stopPropagation()">
                            <a href="{{ route('admin.clients.edit', $client) }}" class="text-ink-secondary hover:text-ink text-sm font-medium transition">עריכה</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <i data-lucide="users" class="w-10 h-10 text-ink-muted/40"></i>
                                <p class="text-ink-muted text-sm">אין לקוחות עדיין.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile cards --}}
    <div class="md:hidden divide-y divide-border">
        @forelse($clients as $client)
            <a href="{{ route('admin.clients.show', $client) }}" class="block p-4 hover:bg-gray-50 transition">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <x-user-avatar :user="$client" size="lg" />
                        <div class="min-w-0">
                            <p class="font-medium text-ink truncate">{{ $client->name }}</p>
                            <p class="text-sm text-ink-secondary truncate">{{ $client->email }}</p>
                        </div>
                    </div>
                    @if($client->is_active)
                        <span class="rounded-badge px-2.5 py-1 text-xs font-medium bg-green-100 text-green-800 whitespace-nowrap">פעיל</span>
                    @else
                        <span class="rounded-badge px-2.5 py-1 text-xs font-medium bg-red-100 text-red-800 whitespace-nowrap">מושבת</span>
                    @endif
                </div>
                <div class="grid grid-cols-2 gap-y-2 gap-x-4 text-sm mb-3">
                    <div>
                        <span class="text-ink-muted text-xs">טלפון</span>
                        <p class="text-ink-secondary">{{ $client->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-ink-muted text-xs">חברה</span>
                        <p class="text-ink-secondary">{{ $client->company ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-ink-muted text-xs">פרויקטים</span>
                        <p class="text-ink-secondary">{{ $client->projects_count }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-end">
                    <span class="text-sm text-ink-secondary font-medium transition">עריכה</span>
                </div>
            </a>
        @empty
            <div class="px-6 py-16 text-center">
                <div class="flex flex-col items-center gap-2">
                    <i data-lucide="users" class="w-10 h-10 text-ink-muted/40"></i>
                    <p class="text-ink-muted text-sm">אין לקוחות עדיין.</p>
                </div>
            </div>
        @endforelse
    </div>

    <div class="px-6 py-4 border-t border-border">
        {{ $clients->links() }}
    </div>
</div>
@endsection
