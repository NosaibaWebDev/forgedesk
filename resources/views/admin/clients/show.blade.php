@extends('layouts.app')

@section('title', $client->name . ' - ' . __('client'))
@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm">
    <a href="{{ route('admin.dashboard') }}" class="text-ink-muted hover:text-accent transition">{{ __('home') }}</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <a href="{{ route('admin.clients.index') }}" class="text-ink-muted hover:text-accent transition">{{ __('clients') }}</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <span class="text-ink font-medium">{{ $client->name }}</span>
</nav>
@endsection

@section('actions')
<div class="flex items-center gap-3">
    @if($client->projects->count())
        @php $lastProject = $client->projects->sortByDesc('updated_at')->first(); @endphp
        <a href="{{ route('admin.messages.show', $lastProject) }}" class="btn-ghost">
            <i data-lucide="message-square" class="w-4 h-4"></i>
            {{ __('send_message') }}
        </a>
    @endif
    <a href="{{ route('admin.clients.edit', $client) }}" class="btn-secondary">
        {{ __('edit_button') }}
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Client Info Card --}}
    <div class="bg-white rounded-card border border-border shadow-card p-6">
        <div class="flex items-center gap-4 mb-6">
            <x-user-avatar :user="$client" size="xl" />
            <div>
                <p class="text-xl font-semibold text-ink">{{ $client->name }}</p>
                <p class="text-sm text-ink-secondary">{{ $client->email }}</p>
            </div>
            <div class="mr-auto">
                @if($client->is_active)
                    <span class="rounded-badge px-3 py-1 text-xs font-medium bg-green-100 text-green-800">{{ __('active') }}</span>
                @else
                    <span class="rounded-badge px-3 py-1 text-xs font-medium bg-red-100 text-red-800">{{ __('inactive') }}</span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-ink-muted">{{ __('phone') }}</p>
                <p class="font-medium text-ink">{{ $client->phone ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-ink-muted">{{ __('company') }}</p>
                <p class="font-medium text-ink">{{ $client->company ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-ink-muted">{{ __('address') }}</p>
                <p class="font-medium text-ink">{{ $client->address ?? '-' }}</p>
            </div>
        </div>

        <div class="mt-4 pt-4 border-t border-border">
            <p class="text-sm text-ink-muted">{{ __('registration_date') }}</p>
            <p class="font-medium text-ink">{{ $client->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    {{-- Projects --}}
    <div class="bg-white rounded-card border border-border shadow-card">
        <div class="flex items-center justify-between p-6 border-b border-border">
            <h3 class="text-lg font-semibold text-ink">{{ __('projects') }} ({{ $client->projects->count() }})</h3>
            <a href="{{ route('admin.projects.create') }}" class="btn-primary btn-sm">
                + {{ __('new_project_button') }}
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead>
                    <tr class="border-b border-border">
                        <th class="px-6 py-3.5 text-xs font-semibold text-ink-muted uppercase tracking-wider">{{ __('project_name') }}</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-ink-muted uppercase tracking-wider">{{ __('status') }}</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-ink-muted uppercase tracking-wider">{{ __('priority') }}</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-ink-muted uppercase tracking-wider">{{ __('progress') }}</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-ink-muted uppercase tracking-wider">{{ __('due_date') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($client->projects as $project)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.projects.show', $project) }}" class="text-accent hover:underline font-medium">
                                    {{ $project->title }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                                        'in_progress' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                                        'review' => 'bg-violet-50 text-violet-700 ring-violet-600/20',
                                        'completed' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                                        'cancelled' => 'bg-red-50 text-red-700 ring-red-600/20',
                                    ];
                                @endphp
                                <span class="rounded-badge px-3 py-1 text-xs font-medium ring-1 ring-inset {{ $statusColors[$project->status->value] ?? '' }}">
                                    {{ $project->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-ink-secondary">{{ $project->priority_label }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 bg-gray-100 rounded-full h-2 w-24">
                                        <div class="bg-accent h-2 rounded-full transition-all" style="width: {{ $project->progress }}%"></div>
                                    </div>
                                    <span class="text-xs text-ink-muted font-medium">{{ $project->progress }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-ink-secondary">
                                {{ $project->due_date ? $project->due_date->format('d/m/Y') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
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
    </div>
</div>
@endsection