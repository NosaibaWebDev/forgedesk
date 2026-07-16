@extends('layouts.app')

@section('title', 'מעקב זמן - מנהל')
@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm">
    <a href="{{ route('admin.dashboard') }}" class="text-ink-muted hover:text-accent transition">בית</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <span class="text-ink font-medium">מעקב זמן</span>
</nav>
@endsection

@section('actions')
<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" @click.outside="open = false" class="btn-ghost inline-flex items-center gap-1">
        <i data-lucide="download" class="w-4 h-4"></i>
        ייצוא
        <i data-lucide="chevron-down" class="w-3 h-3"></i>
    </button>
    <div x-show="open" x-transition x-cloak class="absolute left-0 mt-2 w-48 shadow-elevated z-50 py-1 overflow-hidden" style="background:var(--color-card); border:1px solid var(--color-border); border-radius:12px;">
        <a href="{{ route('admin.timetracker.export.csv', request()->query()) }}" class="dropdown-item"><i data-lucide="file-text"></i>ייצוא CSV</a>
        <a href="{{ route('admin.timetracker.export.pdf', request()->query()) }}" class="dropdown-item" target="_blank"><i data-lucide="file"></i>ייצוא PDF</a>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">
    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-card border border-border shadow-card p-4">
            <p class="text-xs text-ink-muted">היום</p>
            <p class="text-2xl font-bold text-ink mt-1">{{ sprintf('%02d:%02d', floor($totalToday / 60), $totalToday % 60) }}</p>
        </div>
        <div class="bg-white rounded-card border border-border shadow-card p-4">
            <p class="text-xs text-ink-muted">השבוע</p>
            <p class="text-2xl font-bold text-ink mt-1">{{ sprintf('%02d:%02d', floor($totalWeek / 60), $totalWeek % 60) }}</p>
        </div>
        <div class="bg-white rounded-card border border-border shadow-card p-4">
            <p class="text-xs text-ink-muted">החודש</p>
            <p class="text-2xl font-bold text-ink mt-1">{{ sprintf('%02d:%02d', floor($totalMonth / 60), $totalMonth % 60) }}</p>
        </div>
        <div class="{{ $runningEntry ? 'bg-accent/10 border-accent/20' : 'bg-white' }} rounded-card border shadow-card p-4">
            <p class="text-xs text-ink-muted">סטטוס טיימר</p>
            @if($runningEntry)
                <p class="text-lg font-bold text-accent mt-1" id="running-timer" data-start="{{ $runningEntry->start_time->toIso8601String() }}">● רץ...</p>
                <p class="text-xs text-ink-muted mt-1">{{ $runningEntry->description ?? $runningEntry->project?->title ?? 'ללא תיאור' }}</p>
            @else
                <p class="text-lg font-bold text-ink-muted mt-1">עוצר</p>
            @endif
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-card border border-border shadow-card p-4">
        <form method="GET" action="{{ route('admin.timetracker.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[160px]">
                <label class="label">פרויקט</label>
                <select name="project_id" class="input">
                    <option value="">הכל</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[160px]">
                <label class="label">לקוח</label>
                <select name="client_id" class="input">
                    <option value="">הכל</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[140px]">
                <label class="label">מתאריך</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="input">
            </div>
            <div class="min-w-[140px]">
                <label class="label">עד תאריך</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="input">
            </div>
            <button type="submit" class="btn-primary">סנן</button>
            @if(request()->hasAny(['project_id', 'client_id', 'date_from', 'date_to']))
                <a href="{{ route('admin.timetracker.index') }}" class="btn-ghost btn-sm">נקה</a>
            @endif
        </form>
        @if(request()->hasAny(['project_id', 'client_id', 'date_from', 'date_to']))
            <div class="mt-2 pt-2 border-t border-border flex items-center gap-2">
                <span class="text-xs text-ink-muted">סה"כ מסונן:</span>
                <span class="text-sm font-bold text-accent">{{ sprintf('%02d:%02d', floor($totalFiltered / 60), $totalFiltered % 60) }}</span>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Timer + Manual Entry --}}
        <div class="space-y-6">
            {{-- Active Timer --}}
            <div class="bg-white rounded-card border border-border shadow-card p-6">
                <h3 class="text-lg font-semibold text-ink mb-4">טיימר</h3>

                @if($runningEntry)
                    <div class="text-center py-6">
                        <div class="text-5xl font-mono font-bold text-accent mb-4" id="live-timer">00:00:00</div>
                        <p class="text-ink-secondary mb-4">{{ $runningEntry->description ?? $runningEntry->project?->title ?? 'ללא תיאור' }}</p>
                        <form method="POST" action="{{ route('admin.timetracker.stop', $runningEntry) }}">
                            @csrf
                            <button type="submit" class="btn-danger px-8 py-3">
                                ⏹ עצור
                            </button>
                        </form>
                    </div>
                @else
                    <form method="POST" action="{{ route('admin.timetracker.start') }}">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="label">פרויקט</label>
                                <select name="project_id" id="timer-project" onchange="loadTasks(this.value)" class="input">
                                    <option value="">ללא פרויקט</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}">{{ $project->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="label">משימה</label>
                                <select name="task_id" id="timer-task" class="input">
                                    <option value="">ללא משימה</option>
                                </select>
                            </div>
                            <div>
                                <label class="label">תיאור</label>
                                <input type="text" name="description" placeholder="מה אתה עושה?" class="input">
                            </div>
                            <button type="submit" class="btn-primary w-full justify-center flex items-center gap-2">
                                <span class="text-xl">▶</span> התחל טיימר
                            </button>
                        </div>
                    </form>
                @endif
            </div>

            {{-- Manual Entry --}}
            <div class="bg-white rounded-card border border-border shadow-card p-6">
                <h3 class="text-lg font-semibold text-ink mb-4">הוסף ידנית</h3>
                <form method="POST" action="{{ route('admin.timetracker.store') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="label">פרויקט</label>
                            <select name="project_id" class="input">
                                <option value="">ללא פרויקט</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="label">תיאור</label>
                            <input type="text" name="description" placeholder="תיאור העבודה" class="input">
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="label">תאריך</label>
                                <input type="date" name="date" value="{{ now()->format('Y-m-d') }}" required class="input">
                            </div>
                            <div>
                                <label class="label">התחלה</label>
                                <input type="time" name="start_time" required class="input">
                            </div>
                            <div>
                                <label class="label">סיום</label>
                                <input type="time" name="end_time" required class="input">
                            </div>
                        </div>
                        <button type="submit" class="btn-primary w-full justify-center">
                            הוסף רשומה
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Right: Entries List --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-card border border-border shadow-card">
                <div class="p-4 border-b border-border">
                    <h3 class="font-semibold text-ink">רשומות זמן</h3>
                </div>
                <div class="divide-y divide-border max-h-[700px] overflow-y-auto">
                    @forelse($entries as $date => $dayEntries)
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-medium text-ink-secondary text-sm">{{ Carbon\Carbon::parse($date)->locale('he')->isoFormat('dddd, D MMMM YYYY') }}</h4>
                                <span class="text-xs text-ink-muted">
                                    {{ sprintf('%02d:%02d', floor($dayEntries->sum('duration_minutes') / 60), $dayEntries->sum('duration_minutes') % 60) }}
                                </span>
                            </div>
                            <div class="space-y-2">
                                @foreach($dayEntries as $entry)
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3 bg-surface rounded-card {{ $entry->is_running ? 'ring-2 ring-accent' : '' }}">
                                        <div class="flex items-start gap-3 min-w-0">
                                            <span class="text-xs text-ink-muted font-mono shrink-0 w-20 sm:w-24">
                                                {{ $entry->start_time->format('H:i') }}{{ $entry->end_time ? ' - ' . $entry->end_time->format('H:i') : ' - רץ...' }}
                                            </span>
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-ink truncate">{{ $entry->description ?? $entry->project?->title ?? 'ללא תיאור' }}</p>
                                                @if($entry->project)
                                                    <p class="text-xs text-ink-muted truncate">{{ $entry->project->title }}{{ $entry->task ? ' / ' . $entry->task->title : '' }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0">
                                            <span class="text-sm font-mono font-medium {{ $entry->is_running ? 'text-accent' : 'text-ink-secondary' }}">
                                                {{ $entry->is_running ? '● רץ...' : $entry->formatted_duration }}
                                            </span>
                                            @if($entry->is_running)
                                                <form method="POST" action="{{ route('admin.timetracker.stop', $entry) }}">
                                                    @csrf
                                                    <button type="submit" class="text-danger hover:text-red-600 text-sm transition">⏹</button>
                                                </form>
                                            @else
                                                <div class="flex items-center gap-3">
                                                    <a href="{{ route('admin.timetracker.edit', $entry) }}" class="text-ink-secondary hover:text-ink text-sm transition">✏</a>
                                                    <form method="POST" action="{{ route('admin.timetracker.destroy', $entry) }}" onsubmit="return confirm('למחוק רשומה זו?')">
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
                            <p>אין רשומות זמן עדיין.</p>
                            <p class="text-sm mt-1">התחל טיימר או הוסף רשומה ידנית.</p>
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
        taskSelect.innerHTML = '<option value="">טוען...</option>';
        if (!projectId) {
            taskSelect.innerHTML = '<option value="">ללא משימה</option>';
            return;
        }
        fetch('/admin/api/projects/' + projectId + '/tasks')
            .then(r => r.json())
            .then(tasks => {
                taskSelect.innerHTML = '<option value="">ללא משימה</option>';
                tasks.forEach(t => {
                    taskSelect.innerHTML += '<option value="' + t.id + '">' + t.title + '</option>';
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
