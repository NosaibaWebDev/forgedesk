@extends('layouts.app')

@section('title', __('projects_list'))
@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm">
    <a href="{{ route('admin.dashboard') }}" class="text-ink-muted hover:text-accent transition">{{ __('home') }}</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <span class="text-ink font-medium">{{ __('projects') }}</span>
</nav>
@endsection

@section('actions')
<div class="flex items-center gap-3">
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" @click.outside="open = false" class="btn-ghost inline-flex items-center gap-2">
            <i data-lucide="download" class="w-4 h-4"></i>
            {{ __('export') }}
            <i data-lucide="chevron-down" class="w-3 h-3"></i>
        </button>
        <div x-show="open" x-transition x-cloak class="absolute left-0 mt-2 w-48 shadow-elevated z-50 py-1 overflow-hidden" style="background:var(--color-card); border:1px solid var(--color-border); border-radius:12px;">
            <a href="{{ route('admin.projects.export.csv') }}" class="dropdown-item"><i data-lucide="file-text"></i>{{ __('export_csv') }}</a>
            <a href="{{ route('admin.projects.export.pdf') }}" class="dropdown-item" target="_blank"><i data-lucide="file"></i>{{ __('export_pdf') }}</a>
        </div>
    </div>
    <a href="{{ route('admin.projects.create') }}" class="btn-primary">
        + {{ __('new_project') }}
    </a>
</div>
@endsection

@section('content')
<div class="bg-white rounded-card border border-border shadow-card overflow-hidden">
    <div class="px-6 py-5 border-b border-border">
        <h2 class="text-lg font-semibold text-ink">{{ __('projects_list') }}</h2>
    </div>

    {{-- Desktop table --}}
    <div class="overflow-x-auto hidden md:block">
        <table class="w-full text-right">
            <thead>
                <tr class="border-b border-border">
                    <th class="px-6 py-4 text-xs font-semibold text-ink-muted uppercase tracking-wider">{{ __('project_name') }}</th>
                    <th class="px-6 py-4 text-xs font-semibold text-ink-muted uppercase tracking-wider">{{ __('client') }}</th>
                    <th class="px-6 py-4 text-xs font-semibold text-ink-muted uppercase tracking-wider">{{ __('status') }}</th>
                    <th class="px-6 py-4 text-xs font-semibold text-ink-muted uppercase tracking-wider">{{ __('priority') }}</th>
                    <th class="px-6 py-4 text-xs font-semibold text-ink-muted uppercase tracking-wider">{{ __('progress') }}</th>
                    <th class="px-6 py-4 text-xs font-semibold text-ink-muted uppercase tracking-wider">{{ __('due_date') }}</th>
                    <th class="px-6 py-4 text-xs font-semibold text-ink-muted uppercase tracking-wider">{{ __('actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse($projects as $project)
                    <tr class="hover:bg-gray-50 transition cursor-pointer" onclick="window.location.href='{{ route('admin.projects.show', $project) }}'">
                        <td class="px-6 py-5">
                            <span class="text-accent font-medium">{{ $project->title }}</span>
                        </td>
                        <td class="px-6 py-5 text-ink-secondary">{{ $project->user->name }}</td>
                        <td class="px-6 py-5">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                                    'in_progress' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                                    'review' => 'bg-violet-50 text-violet-700 ring-violet-600/20',
                                    'completed' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                                    'cancelled' => 'bg-red-50 text-red-700 ring-red-600/20',
                                ];
                            @endphp
                            <span class="rounded-badge px-3 py-1 text-xs font-medium ring-1 ring-inset {{ $statusColors[$project->status->value] ?? 'bg-gray-50 text-gray-700 ring-gray-600/20' }}">
                                {{ $project->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            @php
                                $priorityColors = [
                                    'low' => 'text-ink-muted',
                                    'medium' => 'text-amber-600',
                                    'high' => 'text-orange-600',
'urgent' => 'text-danger',
                                ];
                            @endphp
                            <span class="text-sm font-medium {{ $priorityColors[$project->priority->value] ?? '' }}">
                                {{ $project->priority_label }}
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="flex-1 bg-accent/10 rounded-full h-2 w-24">
                                    <div class="bg-accent h-2 rounded-full transition-all" style="width: {{ $project->progress }}%"></div>
                                </div>
                                <span class="text-xs text-ink-muted font-medium">{{ $project->progress }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-sm text-ink-secondary">
                            {{ $project->due_date ? $project->due_date->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.projects.edit', $project) }}" class="text-ink-secondary hover:text-ink text-sm font-medium transition" onclick="event.stopPropagation()">{{ __('edit') }}</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <i data-lucide="folder-open" class="w-10 h-10 text-ink-muted/40"></i>
                                <p class="text-ink-muted text-sm">{{ __('no_projects') }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Mobile cards --}}
    <div class="md:hidden divide-y divide-border">
        @forelse($projects as $project)
            <a href="{{ route('admin.projects.show', $project) }}" class="block p-4 hover:bg-gray-50 transition">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div class="min-w-0">
                        <p class="font-medium text-accent truncate">{{ $project->title }}</p>
                        <p class="text-sm text-ink-secondary mt-0.5">{{ $project->user->name }}</p>
                    </div>
                    @php
                        $statusColors = [
                            'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                            'in_progress' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                            'review' => 'bg-violet-50 text-violet-700 ring-violet-600/20',
                            'completed' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                            'cancelled' => 'bg-red-50 text-red-700 ring-red-600/20',
                        ];
                    @endphp
                    <span class="rounded-badge px-2.5 py-1 text-xs font-medium ring-1 ring-inset whitespace-nowrap {{ $statusColors[$project->status->value] ?? 'bg-gray-50 text-gray-700 ring-gray-600/20' }}">
                        {{ $project->status_label }}
                    </span>
                </div>
                <div class="flex items-center justify-between gap-3 text-sm mb-3">
                    @php
                        $priorityColors = [
                            'low' => 'text-ink-muted',
                            'medium' => 'text-amber-600',
                            'high' => 'text-orange-600',
                            'urgent' => 'text-red-600',
                        ];
                    @endphp
                    <span class="font-medium {{ $priorityColors[$project->priority->value] ?? '' }}">
                        {{ $project->priority_label }}
                    </span>
                    <span class="text-ink-secondary">
                        {{ $project->due_date ? $project->due_date->format('d/m/Y') : '-' }}
                    </span>
                </div>
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex-1 bg-accent/10 rounded-full h-2">
                        <div class="bg-accent h-2 rounded-full transition-all" style="width: {{ $project->progress }}%"></div>
                    </div>
                    <span class="text-xs text-ink-muted font-medium">{{ $project->progress }}%</span>
                </div>
                <div class="flex items-center justify-end">
                    <span class="text-sm text-ink-secondary font-medium transition">{{ __('edit') }}</span>
                </div>
            </a>
        @empty
            <div class="px-6 py-16 text-center">
                <div class="flex flex-col items-center gap-2">
                    <i data-lucide="folder-open" class="w-10 h-10 text-ink-muted/40"></i>
                    <p class="text-ink-muted text-sm">{{ __('no_projects') }}</p>
                </div>
            </div>
        @endforelse
    </div>

    @if($projects->hasPages())
    <div class="px-6 py-4 border-t border-border">
        {{ $projects->links() }}
    </div>
    @endif
</div>
@endsection