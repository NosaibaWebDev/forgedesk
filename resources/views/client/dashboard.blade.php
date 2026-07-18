@extends('layouts.app')

@section('title', __('client_dashboard'))
@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm">
    <span class="text-ink font-medium">{{ __('hello') }} {{ auth()->user()->name }}</span>
</nav>
@endsection

@section('content')
{{-- Stats Row --}}
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
    <a href="{{ route('client.projects.index') }}" class="bg-white rounded-card border border-border shadow-card p-5 hover:shadow-lg hover:border-accent/30 transition group">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-btn bg-accent/10 flex items-center justify-center">
                <i data-lucide="folder-kanban" class="w-5 h-5 text-accent"></i>
            </div>
            <span class="text-xs text-ink-muted">{{ __('my_projects') }}</span>
        </div>
        <p class="text-3xl font-bold text-ink">{{ $totalProjects }}</p>
        <p class="text-xs text-accent font-medium mt-1">{{ $activeProjects }} {{ __('active_projects') }}</p>
    </a>
    <div class="bg-white rounded-card border border-border shadow-card p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-btn bg-green-50 flex items-center justify-center">
                <i data-lucide="check-circle-2" class="w-5 h-5 text-green-600"></i>
            </div>
            <span class="text-xs text-ink-muted">{{ __('completed_stat') }}</span>
        </div>
        <p class="text-3xl font-bold text-green-600">{{ $completedProjects }}</p>
    </div>
    <div class="bg-white rounded-card border border-border shadow-card p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-btn bg-accent/10 flex items-center justify-center">
                <i data-lucide="banknote" class="w-5 h-5 text-accent"></i>
            </div>
            <span class="text-xs text-ink-muted">{{ __('total_expenses') }}</span>
        </div>
        <p class="text-3xl font-bold text-ink">₪{{ number_format($totalSpent, 0) }}</p>
    </div>
    <a href="{{ route('client.messages.index') }}" class="bg-white rounded-card border border-border shadow-card p-5 hover:shadow-lg hover:border-accent/30 transition group">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-btn bg-accent/10 flex items-center justify-center">
                <i data-lucide="mail" class="w-5 h-5 text-accent"></i>
            </div>
            <span class="text-xs text-ink-muted">{{ __('messages') }}</span>
        </div>
        <p class="text-3xl font-bold {{ $unreadMessages > 0 ? 'text-danger' : 'text-ink' }}">{{ $unreadMessages }}</p>
        <p class="text-xs text-ink-muted mt-1">{{ __('new_messages') }}</p>
    </a>
    <div class="bg-accent/5 rounded-card border border-accent/20 p-5 flex flex-col items-center justify-center">
        <div class="w-10 h-10 rounded-btn bg-accent/10 flex items-center justify-center mb-2">
            <i data-lucide="shield-check" class="w-5 h-5 text-accent"></i>
        </div>
        <p class="text-xs text-accent font-medium">{{ __('account_status') }}</p>
        <p class="text-lg font-bold text-ink mt-1">{{ __('active') }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left Column: Projects --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- My Projects --}}
        <div class="bg-white rounded-card border border-border shadow-card">
            <div class="flex items-center justify-between px-6 py-4 border-b border-border">
                <h3 class="font-semibold text-ink">{{ __('my_projects') }}</h3>
                <a href="{{ route('client.projects.index') }}" class="text-sm text-accent hover:text-accent-dark font-medium transition">{{ __('all') }} ←</a>
            </div>
            <div class="divide-y divide-border">
                @forelse($recentProjects as $project)
                    <a href="{{ route('client.projects.show', $project) }}" class="block px-6 py-5 hover:bg-gray-50 transition">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-medium text-ink">{{ $project->title }}</h4>
                            @php
                                $statusColors = [
                                    'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                                    'in_progress' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                                    'review' => 'bg-purple-50 text-purple-700 ring-purple-600/20',
                                    'completed' => 'bg-green-50 text-green-700 ring-green-600/20',
                                    'cancelled' => 'bg-red-50 text-red-700 ring-red-600/20',
                                ];
                            @endphp
                            <span class="rounded-badge px-3 py-1 text-xs font-medium ring-1 ring-inset {{ $statusColors[$project->status->value] ?? '' }}">
                                {{ $project->status_label }}
                            </span>
                        </div>
                        <p class="text-sm text-ink-muted mb-3 line-clamp-2">{{ $project->description ?? __('no_description') }}</p>
                        <div class="flex items-center gap-4">
                            <div class="flex-1">
                                <div class="flex items-center justify-between text-xs text-ink-muted mb-1.5">
                                    <span>{{ __('progress') }}</span>
                                    <span class="font-medium text-ink-secondary">{{ $project->progress }}%</span>
                                </div>
                                <div class="bg-accent/10 rounded-full h-1.5">
                                    <div class="bg-accent h-1.5 rounded-full transition-all" style="width: {{ $project->progress }}%"></div>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 mr-4 text-xs text-ink-muted">
                                <span>{{ $project->total_tasks_count ?? 0 }} {{ __('tasks') }}</span>
                                @if($project->due_date)
                                    <span class="{{ $project->due_date->isPast() ? 'text-danger font-medium' : '' }}">
                                        {{ __('due_date') }}: {{ $project->due_date->format('d/m') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="p-12 text-center text-ink-muted">
                        <i data-lucide="folder-kanban" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                        <p>{{ __('no_projects_yet') }}</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Upcoming Deadlines --}}
        @if($upcomingDeadlines->count())
        <div class="bg-white rounded-card border border-border shadow-card">
            <div class="flex items-center justify-between px-6 py-4 border-b border-border">
                <h3 class="font-semibold text-ink flex items-center gap-2">
                    <i data-lucide="clock" class="w-4 h-4 text-orange-500"></i>
                    {{ __('upcoming_dates') }}
                </h3>
            </div>
            <div class="divide-y divide-border">
                @foreach($upcomingDeadlines as $task)
                    <a href="{{ route('client.projects.show', $task->project) }}" class="block px-6 py-4 hover:bg-gray-50 transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-ink text-sm">{{ $task->title }}</p>
                                <p class="text-xs text-ink-muted">{{ $task->project->title }}</p>
                            </div>
                            @php
                                $daysLeft = round(now()->diffInDays($task->due_date, false));
                                $isOverdue = $daysLeft < 0;
                            @endphp
                            <span class="text-xs font-medium {{ $isOverdue ? 'text-danger' : ($daysLeft <= 3 ? 'text-orange-600' : 'text-ink-secondary') }}">
                                {{ $isOverdue ? __('overdue') . ' ' . abs($daysLeft) . ' ' . __('days') : __('in_days') . ' ' . $daysLeft . ' ' . __('days') }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Right Column: Messages --}}
    <div class="space-y-6">
        {{-- Recent Messages --}}
        <div class="bg-white rounded-card border border-border shadow-card">
            <div class="flex items-center justify-between px-6 py-4 border-b border-border">
                <h3 class="font-semibold text-ink">{{ __('recent_messages') }}</h3>
                <a href="{{ route('client.messages.index') }}" class="text-sm text-accent hover:text-accent-dark font-medium transition">{{ __('all') }} ←</a>
            </div>
            <div class="divide-y divide-border">
                @forelse($recentMessages as $message)
                    <a href="{{ route('client.messages.show', $message->project) }}" class="block px-6 py-4 hover:bg-gray-50 transition">
                        <div class="flex items-start gap-3">
                            <x-user-avatar :user="$message->sender" size="sm" :variant="$message->sender_id === auth()->id() ? 'accent' : 'green'" />
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-ink truncate">{{ $message->sender->name }}</p>
                                    <span class="text-xs text-ink-muted flex-shrink-0">{{ $message->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-xs text-ink-muted truncate mt-0.5">{{ $message->project->title }}</p>
                                <p class="text-sm text-ink-secondary truncate mt-1">{{ $message->body }}</p>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center text-ink-muted text-sm">
                        {{ __('no_messages') }}
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Quick Info --}}
        <div class="bg-white rounded-card border border-border shadow-card p-6">
            <h3 class="font-semibold text-ink mb-4">{{ __('account_details') }}</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between items-center py-2 border-b border-border/50 last:border-0">
                    <span class="text-ink-muted">{{ __('email') }}</span>
                    <span class="text-ink font-medium">{{ auth()->user()->email }}</span>
                </div>
                @if(auth()->user()->phone)
                <div class="flex justify-between items-center py-2 border-b border-border/50 last:border-0">
                    <span class="text-ink-muted">{{ __('phone') }}</span>
                    <span class="text-ink font-medium">{{ auth()->user()->phone }}</span>
                </div>
                @endif
                @if(auth()->user()->company)
                <div class="flex justify-between items-center py-2 border-b border-border/50 last:border-0">
                    <span class="text-ink-muted">{{ __('company') }}</span>
                    <span class="text-ink font-medium">{{ auth()->user()->company }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
