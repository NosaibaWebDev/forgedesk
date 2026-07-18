@extends('layouts.app')

@section('title', __('time_tracker_title'))
@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm">
    <a href="{{ route('admin.dashboard') }}" class="text-ink-muted hover:text-accent transition hidden sm:inline">{{ __('home') }}</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted hidden sm:block"></i>
    <span class="text-ink font-medium">{{ __('time_tracking') }}</span>
</nav>
@endsection

@section('actions')
<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" @click.outside="open = false" class="btn-ghost inline-flex items-center gap-1">
        <i data-lucide="download" class="w-4 h-4"></i>
        <span class="hidden sm:inline">{{ __('export') }}</span>
        <i data-lucide="chevron-down" class="hidden sm:block w-3 h-3"></i>
    </button>
    <div x-show="open" x-transition x-cloak class="absolute start-0 mt-2 w-48 shadow-elevated z-50 py-1 overflow-hidden" style="background:var(--color-card); border:1px solid var(--color-border); border-radius:12px;">
        <a href="{{ route('admin.timetracker.export.csv', request()->query()) }}" class="dropdown-item"><i data-lucide="file-text"></i>{{ __('export_csv') }}</a>
        <a href="{{ route('admin.timetracker.export.pdf', request()->query()) }}" class="dropdown-item" target="_blank"><i data-lucide="file"></i>{{ __('export_pdf') }}</a>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">
    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-card border border-border shadow-card p-4">
            <p class="text-xs text-ink-muted">{{ __('today') }}</p>
            <p class="text-2xl font-bold text-ink mt-1">{{ sprintf('%02d:%02d', floor($totalToday / 60), $totalToday % 60) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-card border border-border shadow-card p-4">
            <p class="text-xs text-ink-muted">{{ __('this_week') }}</p>
            <p class="text-2xl font-bold text-ink mt-1">{{ sprintf('%02d:%02d', floor($totalWeek / 60), $totalWeek % 60) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-card border border-border shadow-card p-4">
            <p class="text-xs text-ink-muted">{{ __('this_month') }}</p>
            <p class="text-2xl font-bold text-ink mt-1">{{ sprintf('%02d:%02d', floor($totalMonth / 60), $totalMonth % 60) }}</p>
        </div>
        <div class="{{ $runningEntry ? 'bg-accent/10 border-accent/20' : 'bg-white dark:bg-gray-800' }} rounded-card border shadow-card p-4">
            <p class="text-xs text-ink-muted">{{ __('timer_status') }}</p>
            @if($runningEntry)
                <p class="text-lg font-bold text-accent mt-1" id="running-timer" data-start="{{ $runningEntry->start_time->toIso8601String() }}">● {{ __('running') }}</p>
                <p class="text-xs text-ink-muted mt-1">{{ $runningEntry->description ?? $runningEntry->project?->title ?? __('no_description') }}</p>
            @else
                <p class="text-lg font-bold text-ink-muted mt-1">{{ __('stopping') }}</p>
            @endif
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-card border border-border shadow-card p-4">
        <form method="GET" action="{{ route('admin.timetracker.index') }}" class="flex flex-col sm:flex-row sm:flex-wrap items-stretch sm:items-end gap-3">
            <div class="flex-1 sm:min-w-[160px]">
                <label class="label">{{ __('project_name') }}</label>
                <select name="project_id" class="input">
                    <option value="">{{ __('all') }}</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 sm:min-w-[160px]">
                <label class="label">{{ __('client') }}</label>
                <select name="client_id" class="input">
                    <option value="">{{ __('all') }}</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:min-w-[140px]">
                <label class="label">{{ __('from_date') }}</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="input" style="max-width:305px">
            </div>
            <div class="sm:min-w-[140px]">
                <label class="label">{{ __('to_date') }}</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="input" style="max-width:305px">
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn-primary flex-1 sm:flex-none">{{ __('filter') }}</button>
                @if(request()->hasAny(['project_id', 'client_id', 'date_from', 'date_to']))
                    <a href="{{ route('admin.timetracker.index') }}" class="btn-ghost btn-sm">{{ __('clear') }}</a>
                @endif
            </div>
        </form>
        @if(request()->hasAny(['project_id', 'client_id', 'date_from', 'date_to']))
            <div class="mt-2 pt-2 border-t border-border flex items-center gap-2">
                <span class="text-xs text-ink-muted">{{ __('filtered_total') }}</span>
                <span class="text-sm font-bold text-accent">{{ sprintf('%02d:%02d', floor($totalFiltered / 60), $totalFiltered % 60) }}</span>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Timer + Manual Entry --}}
        <div class="space-y-6">
            {{-- Active Timer --}}
            <div class="bg-white dark:bg-gray-800 rounded-card border border-border shadow-card p-6">
                <h3 class="text-lg font-semibold text-ink mb-4">{{ __('timer') }}</h3>

                @if($runningEntry)
                    <div class="text-center py-6">
                        <div class="text-5xl font-mono font-bold text-accent mb-4" id="live-timer">00:00:00</div>
                        <p class="text-ink-secondary mb-4">{{ $runningEntry->description ?? $runningEntry->project?->title ?? __('no_description') }}</p>
                        <form method="POST" action="{{ route('admin.timetracker.stop', $runningEntry) }}">
                            @csrf
                            <button type="submit" class="btn-danger px-8 py-3">
                                ⏹ {{ __('stop_timer') }}
                            </button>
                        </form>
                    </div>
                @else
                    <form method="POST" action="{{ route('admin.timetracker.start') }}">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="label">{{ __('project_name') }}</label>
                                <select name="project_id" id="timer-project" onchange="loadTasks(this.value)" class="input">
                                    <option value="">{{ __('no_project') }}</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}">{{ $project->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="label">{{ __('task_label') }}</label>
                                <select name="task_id" id="timer-task" class="input">
                                    <option value="">{{ __('no_task') }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="label">{{ __('description') }}</label>
                                <input type="text" name="description" placeholder="{{ __('what_are_you_doing') }}" class="input">
                            </div>
                            <button type="submit" class="btn-primary w-full justify-center flex items-center gap-2">
                                <span class="text-xl">▶</span> {{ __('start_timer') }}
                            </button>
                        </div>
                    </form>
                @endif
            </div>

            {{-- Manual Entry --}}
            <div class="bg-white dark:bg-gray-800 rounded-card border border-border shadow-card p-6">
                <h3 class="text-lg font-semibold text-ink mb-4">{{ __('add_manually') }}</h3>
                <form method="POST" action="{{ route('admin.timetracker.store') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="label">{{ __('project_name') }}</label>
                            <select name="project_id" class="input">
                                <option value="">{{ __('no_project') }}</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="label">{{ __('description') }}</label>
                            <input type="text" name="description" placeholder="{{ __('description_placeholder') }}" class="input">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="label">{{ __('date') }}</label>
                                <input type="date" name="date" value="{{ now()->format('Y-m-d') }}" required class="input" style="max-width:305px">
                            </div>
                            <div>
                                <label class="label">{{ __('start_time') }}</label>
                                <input type="time" name="start_time" required class="input" style="max-width:305px">
                            </div>
                            <div>
                                <label class="label">{{ __('end_time') }}</label>
                                <input type="time" name="end_time" required class="input" style="max-width:305px">
                            </div>
                        </div>
                        <button type="submit" class="btn-primary w-full justify-center">
                            {{ __('manual_entry') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Right: Entries List --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-card border border-border shadow-card">
                <div class="p-4 border-b border-border">
                    <h3 class="font-semibold text-ink">{{ __('time_entries') }}</h3>
                </div>
                <div class="divide-y divide-border max-h-[700px] overflow-y-auto">
                    @forelse($entries as $date => $dayEntries)
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-medium text-ink-secondary text-sm">{{ Carbon\Carbon::parse($date)->locale('he')->isoFormat('dddd, D MMMM YYYY') }}</h4>
                                <span class="text-xs text-ink-muted">
                                    {{ round($dayEntries->sum('duration_minutes') / 1440) }} {{ __('days') }}
                                </span>
                            </div>
                            <div class="space-y-2">
                                @foreach($dayEntries as $entry)
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3 bg-surface rounded-card {{ $entry->is_running ? 'ring-2 ring-accent' : '' }}">
                                        <div class="flex items-start gap-3 min-w-0">
                                            <div class="shrink-0 text-xs text-ink-muted">
                                                <span class="text-ink-secondary font-medium block">{{ $entry->start_time->format('d/m/Y') }}</span>
                                                <span class="font-mono">{{ $entry->start_time->format('H:i') }}{{ $entry->end_time ? ' - ' . $entry->end_time->format('H:i') : ' - ' . __('running') }}</span>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-ink truncate">{{ $entry->description ?? $entry->project?->title ?? __('no_description') }}</p>
                                                @if($entry->project)
                                                    <p class="text-xs text-ink-muted truncate">{{ $entry->project->title }}{{ $entry->task ? ' / ' . $entry->task->title : '' }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0">
                                            <span class="text-sm font-mono font-medium {{ $entry->is_running ? 'text-accent' : 'text-ink-secondary' }}">
                                                {{ $entry->is_running ? '● ' . __('running') : $entry->formatted_duration }}
                                            </span>
                                            @if($entry->is_running)
                                                <form method="POST" action="{{ route('admin.timetracker.stop', $entry) }}">
                                                    @csrf
                                                    <button type="submit" class="text-danger hover:text-red-600 text-sm transition">⏹</button>
                                                </form>
                                            @else
                                                <div class="flex items-center gap-3">
                                                    <a href="{{ route('admin.timetracker.edit', $entry) }}" class="text-ink-secondary hover:text-ink text-sm transition">✏</a>
                                                    <form method="POST" action="{{ route('admin.timetracker.destroy', $entry) }}" onsubmit="return confirm('{{ __("confirm_delete_entry") }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-ink-muted hover:text-danger text-sm transition">✕</button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center text-ink-muted">
                            <i data-lucide="clock" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                            <p>{{ __('no_time_entries') }}</p>
                            <p class="text-sm mt-1">{{ __('start_or_add_manually') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function loadTasks(projectId) {
        const taskSelect = document.getElementById('timer-task');
        taskSelect.innerHTML = '';
        const loadingOpt = document.createElement('option');
        loadingOpt.value = '';
        loadingOpt.textContent = '{{ __("loading") }}';
        taskSelect.appendChild(loadingOpt);
        if (!projectId) {
            taskSelect.innerHTML = '';
            const emptyOpt = document.createElement('option');
            emptyOpt.value = '';
            emptyOpt.textContent = '{{ __("no_task") }}';
            taskSelect.appendChild(emptyOpt);
            return;
        }
        fetch('/admin/api/projects/' + projectId + '/tasks')
            .then(r => r.json())
            .then(tasks => {
                taskSelect.innerHTML = '';
                const defaultOpt = document.createElement('option');
                defaultOpt.value = '';
                defaultOpt.textContent = '{{ __("no_task") }}';
                taskSelect.appendChild(defaultOpt);
                tasks.forEach(t => {
                    const opt = document.createElement('option');
                    opt.value = t.id;
                    opt.textContent = t.title;
                    taskSelect.appendChild(opt);
                });
            });
    }

    @if($runningEntry)
    function updateTimer() {
        const start = new Date('{{ $runningEntry->start_time->toIso8601String() }}');
        const now = new Date();
        const diff = Math.floor((now - start) / 1000);
        const h = String(Math.floor(diff / 3600)).padStart(2, '0');
        const m = String(Math.floor((diff % 3600) / 60)).padStart(2, '0');
        const s = String(diff % 60).padStart(2, '0');
        document.getElementById('live-timer').textContent = h + ':' + m + ':' + s;
    }
    setInterval(updateTimer, 1000);
    updateTimer();
    @endif
</script>
@endpush
@endsection