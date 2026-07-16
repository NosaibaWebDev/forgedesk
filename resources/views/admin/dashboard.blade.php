@extends('layouts.app')

@section('title', 'לוח בקרה - מנהל')
@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm">
    <a href="{{ route('admin.dashboard') }}" class="text-ink-muted hover:text-accent transition">בית</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <span class="text-ink font-medium">לוח בקרה</span>
</nav>
@endsection

@section('actions')
<a href="{{ route('admin.projects.create') }}" class="btn-primary inline-flex items-center gap-2">
    <i data-lucide="plus" class="w-4 h-4"></i>
    פרויקט חדש
</a>
@endsection

@section('content')
{{-- Stats Row --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    <a href="{{ route('admin.projects.index') }}" class="bg-white rounded-card border border-border shadow-card p-4 hover:shadow-md transition">
        <p class="text-xs text-ink-muted">פרויקטים</p>
        <p class="text-2xl font-bold text-ink mt-1">{{ $totalProjects }}</p>
        <p class="text-xs text-accent mt-1">{{ $activeProjects }} פעילים</p>
    </a>
    <a href="{{ route('admin.clients.index') }}" class="bg-white rounded-card border border-border shadow-card p-4 hover:shadow-md transition">
        <p class="text-xs text-ink-muted">לקוחות</p>
        <p class="text-2xl font-bold text-ink mt-1">{{ $totalClients }}</p>
    </a>
    <div class="bg-white rounded-card border border-border shadow-card p-4">
        <p class="text-xs text-ink-muted">משימות ממתינות</p>
        <p class="text-2xl font-bold text-orange-600 mt-1">{{ $pendingTasks }}</p>
    </div>
    <div class="bg-white rounded-card border border-border shadow-card p-4">
        <p class="text-xs text-ink-muted">הכנסה</p>
        <p class="text-2xl font-bold text-ink mt-1">₪{{ number_format($totalRevenue, 0) }}</p>
    </div>
    <a href="{{ route('admin.messages.index') }}" class="bg-white rounded-card border border-border shadow-card p-4 hover:shadow-md transition">
        <p class="text-xs text-ink-muted">הודעות</p>
        <p class="text-2xl font-bold {{ $unreadMessages > 0 ? 'text-danger' : 'text-ink' }} mt-1">{{ $unreadMessages }}</p>
        <p class="text-xs text-ink-muted mt-1">לא נקראו</p>
    </a>
    <a href="{{ route('admin.projects.create') }}" class="bg-accent/10 rounded-card border border-accent/20 p-4 hover:bg-accent/15 transition flex flex-col items-center justify-center">
        <i data-lucide="plus" class="w-8 h-8 text-accent"></i>
        <p class="text-xs text-accent mt-1 font-medium">פרויקט חדש</p>
    </a>
</div>

{{-- Kanban Board --}}
<div class="mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-ink text-lg">סטטוס פרויקטים — גרור לשינוי</h3>
        <div class="flex items-center gap-2">
            <button onclick="clearAllProjects()" class="text-ink-secondary hover:bg-gray-50 rounded-btn px-4 py-2 text-sm transition inline-flex items-center gap-1.5">
                <i data-lucide="x" class="w-4 h-4"></i>
                נקה הכל
            </button>
            <a href="{{ route('admin.projects.index') }}" class="text-sm text-accent hover:text-accent-dark transition">הכל ←</a>
        </div>
    </div>

    @php
        $columns = [
            'pending' => ['label' => 'ממתין', 'color' => 'yellow', 'bg' => 'bg-yellow-50', 'border' => 'border-yellow-200', 'text' => 'text-yellow-700', 'dot' => 'bg-yellow-500'],
            'in_progress' => ['label' => 'בתהליך', 'color' => 'blue', 'bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'text' => 'text-blue-700', 'dot' => 'bg-blue-500'],
            'review' => ['label' => 'בבדיקה', 'color' => 'purple', 'bg' => 'bg-purple-50', 'border' => 'border-purple-200', 'text' => 'text-purple-700', 'dot' => 'bg-purple-500'],
            'completed' => ['label' => 'הושלם', 'color' => 'green', 'bg' => 'bg-green-50', 'border' => 'border-green-200', 'text' => 'text-green-700', 'dot' => 'bg-green-500'],
        ];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($columns as $status => $col)
            <div class="kanban-column bg-white rounded-card border border-border min-h-[300px] transition-colors"
                 data-status="{{ $status }}"
                 ondragover="event.preventDefault(); this.classList.add('ring-2', 'ring-accent/30')"
                 ondragleave="this.classList.remove('ring-2', 'ring-accent/30')"
                 ondrop="handleDrop(event, '{{ $status }}')">
                <div class="px-4 py-3 border-b border-border flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full {{ $col['dot'] }}"></span>
                        <h4 class="font-medium text-sm text-ink">{{ $col['label'] }}</h4>
                    </div>
                    <span class="rounded-badge px-2.5 py-0.5 text-xs font-medium {{ $col['bg'] }} {{ $col['text'] }}" id="count-{{ $status }}">
                        {{ $activeProjectsList->where('status', $status)->count() }}
                    </span>
                </div>
                <div class="p-3 space-y-2" id="column-{{ $status }}">
                    @foreach($activeProjectsList->where('status', $status) as $project)
                        <div class="kanban-card bg-white rounded-card p-4 border border-border cursor-grab hover:shadow-md transition-all select-none"
                             draggable="true"
                             oncontextmenu="return false;"
                             data-project-id="{{ $project->id }}"
                             ondragstart="handleDragStart(event)">
                            <div class="flex items-center justify-between mb-3">
                                <a href="{{ route('admin.projects.show', $project) }}" class="font-medium text-sm text-ink hover:text-accent truncate flex-1">{{ $project->title }}</a>
                                <button onclick="removeProject({{ $project->id }})" class="text-ink-muted hover:text-danger flex-shrink-0 ml-1 transition" title="הסר מהלוח">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                            </div>
                            <p class="text-xs text-ink-secondary mb-3">{{ $project->user->name }}</p>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-accent h-1.5 rounded-full transition-all" style="width: {{ $project->progress }}%"></div>
                                </div>
                                <span class="text-xs text-ink-muted">{{ $project->progress }}%</span>
                            </div>
                            @if($project->due_date)
                                <p class="text-xs {{ $project->due_date->isPast() ? 'text-danger font-medium' : 'text-ink-muted' }} mt-2">
                                    {{ $project->due_date->format('d/m/Y') }}{{ $project->due_date->isPast() ? ' — באיחור' : '' }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Left Column: Urgent Tasks + Deadlines --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Urgent Tasks --}}
        <div class="bg-white rounded-card border border-border shadow-card">
            <div class="flex items-center justify-between px-6 py-4 border-b border-border">
                <h3 class="font-semibold text-ink flex items-center gap-2">
                    <span class="w-2 h-2 bg-danger rounded-full"></span>
                    משימות דחופות
                </h3>
            </div>
            <div class="divide-y divide-border">
                @forelse($urgentTasks as $task)
                    <a href="{{ route('admin.projects.show', $task->project) }}" class="block px-6 py-4 hover:bg-gray-50 transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-ink text-sm">{{ $task->title }}</p>
                                <p class="text-xs text-ink-muted mt-0.5">{{ $task->project->title }}</p>
                            </div>
                            @php
                                $taskStatusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'in_progress' => 'bg-blue-100 text-blue-800',
                                ];
                            @endphp
                            <span class="rounded-badge px-3 py-1 text-xs font-medium {{ $taskStatusColors[$task->status] ?? '' }}">
                                {{ $task->status_label }}
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="px-6 py-8 text-center text-ink-muted text-sm">
                        אין משימות דחופות כרגע
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Upcoming Deadlines --}}
        <div class="bg-white rounded-card border border-border shadow-card">
            <div class="flex items-center justify-between px-6 py-4 border-b border-border">
                <h3 class="font-semibold text-ink flex items-center gap-2">
                    <i data-lucide="clock" class="w-5 h-5 text-orange-500"></i>
                    תאריכי יעד קרובים
                </h3>
            </div>
            <div class="divide-y divide-border">
                @forelse($upcomingDeadlines as $task)
                    <a href="{{ route('admin.projects.show', $task->project) }}" class="block px-6 py-4 hover:bg-gray-50 transition">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-ink text-sm">{{ $task->title }}</p>
                                <p class="text-xs text-ink-muted mt-0.5">{{ $task->project->title }}</p>
                            </div>
                            <div class="text-left">
                                @php
                                    $daysLeft = now()->diffInDays($task->due_date, false);
                                    $isOverdue = $daysLeft < 0;
                                @endphp
                                <span class="text-xs font-medium {{ $isOverdue ? 'text-danger' : ($daysLeft <= 2 ? 'text-orange-600' : 'text-ink-secondary') }}">
                                    {{ $isOverdue ? 'באיחור ' . abs($daysLeft) . ' ימים' : 'בעוד ' . $daysLeft . ' ימים' }}
                                </span>
                                <p class="text-xs text-ink-muted mt-0.5">{{ $task->due_date->format('d/m') }}</p>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="px-6 py-8 text-center text-ink-muted text-sm">
                        אין תאריכי יעד קרובים
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Right Column: Messages + Clients --}}
    <div class="space-y-6">
        {{-- Unread Messages --}}
        <div class="bg-white rounded-card border border-border shadow-card">
            <div class="flex items-center justify-between px-6 py-4 border-b border-border">
                <h3 class="font-semibold text-ink">הודעות אחרונות</h3>
                <a href="{{ route('admin.messages.index') }}" class="text-sm text-accent hover:text-accent-dark transition">הכל ←</a>
            </div>
            <div class="divide-y divide-border">
                @forelse($recentMessages as $message)
                    <a href="{{ route('admin.messages.show', $message->project) }}" class="block px-6 py-4 hover:bg-gray-50 transition">
                        <div class="flex items-start gap-3">
                            <x-user-avatar :user="$message->sender" size="sm" />
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
                    <div class="px-6 py-8 text-center text-ink-muted text-sm">
                        אין הודעות חדשות
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Recent Clients --}}
        <div class="bg-white rounded-card border border-border shadow-card">
            <div class="flex items-center justify-between px-6 py-4 border-b border-border">
                <h3 class="font-semibold text-ink">לקוחות אחרונים</h3>
                <a href="{{ route('admin.clients.index') }}" class="text-sm text-accent hover:text-accent-dark transition">הכל ←</a>
            </div>
            <div class="divide-y divide-border">
                @forelse($recentClients as $client)
                    <a href="{{ route('admin.clients.show', $client) }}" class="flex items-center gap-3 px-6 py-4 hover:bg-gray-50 transition">
                        <x-user-avatar :user="$client" />
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-ink">{{ $client->name }}</p>
                            <p class="text-xs text-ink-muted">{{ $client->email }}</p>
                        </div>
                        <span class="text-xs text-ink-muted">{{ $client->projects_count }} פרויקטים</span>
                    </a>
                @empty
                    <div class="px-6 py-8 text-center text-ink-muted text-sm">
                        אין לקוחות עדיין
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let draggedProjectId = null;

    function handleDragStart(event) {
        draggedProjectId = event.target.dataset.projectId;
        event.target.style.opacity = '0.5';
        event.dataTransfer.effectAllowed = 'move';
        setTimeout(() => { event.target.style.opacity = '1'; }, 0);
    }

    document.querySelectorAll('.kanban-card').forEach(card => {
        card.addEventListener('dragend', function() {
            this.style.opacity = '1';
            document.querySelectorAll('.kanban-column').forEach(col => {
                col.classList.remove('ring-2', 'ring-accent/30');
            });
        });
    });

    function handleDrop(event, newStatus) {
        event.preventDefault();
        const column = event.currentTarget;
        column.classList.remove('ring-2', 'ring-accent/30');

        if (!draggedProjectId) return;

        updateProjectStatus(draggedProjectId, newStatus);
    }

    function updateProjectStatus(projectId, status) {
        const token = document.querySelector('meta[name="csrf-token"]').content;
        fetch('/admin/api/projects/' + projectId + '/status', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ status: status }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) location.reload();
        });
    }

    function removeProject(projectId) {
        if (!confirm('להסיר פרויקט זה מהלוח?')) return;
        const token = document.querySelector('meta[name="csrf-token"]').content;
        fetch('/admin/api/dashboard/projects/' + projectId + '/remove', {
            method: 'PUT',
            headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) location.reload();
        });
    }

    function clearAllProjects() {
        if (!confirm('להסיר את כל הפרויקטים מהלוח?')) return;
        const token = document.querySelector('meta[name="csrf-token"]').content;
        fetch('/admin/api/dashboard/clear-all', {
            method: 'PUT',
            headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) location.reload();
        });
    }

    // Mobile touch drag-and-drop support
    (function initTouchDrag() {
        const cards = document.querySelectorAll('.kanban-card');
        const columns = document.querySelectorAll('.kanban-column');
        let activeCard = null;
        let ghostCard = null;
        let longPressTimer = null;
        let touchStartPos = null;
        let isDragging = false;
        let scrollInterval = null;
        const LONG_PRESS_MS = 400;
        const MOVE_THRESHOLD = 10;

        function endDrag() {
            if (!isDragging) return;
            isDragging = false;
            if (ghostCard) {
                ghostCard.remove();
                ghostCard = null;
            }
            columns.forEach(col => col.classList.remove('ring-2', 'ring-accent/30'));
            if (activeCard) {
                activeCard.style.opacity = '1';
                activeCard = null;
            }
            draggedProjectId = null;
            if (scrollInterval) {
                clearInterval(scrollInterval);
                scrollInterval = null;
            }
        }

        function getColumnAtPoint(x, y) {
            for (const col of columns) {
                const rect = col.getBoundingClientRect();
                if (x >= rect.left && x <= rect.right && y >= rect.top && y <= rect.bottom) {
                    return col;
                }
            }
            return null;
        }

        function updateScroll(y) {
            const margin = 80;
            const scrollSpeed = 8;
            if (scrollInterval) {
                clearInterval(scrollInterval);
                scrollInterval = null;
            }
            if (y < margin) {
                scrollInterval = setInterval(() => window.scrollBy(0, -scrollSpeed), 16);
            } else if (y > window.innerHeight - margin) {
                scrollInterval = setInterval(() => window.scrollBy(0, scrollSpeed), 16);
            }
        }

        cards.forEach(card => {
            card.addEventListener('touchstart', function(e) {
                const touch = e.touches[0];
                touchStartPos = { x: touch.clientX, y: touch.clientY };
                activeCard = this;
                isDragging = false;

                longPressTimer = setTimeout(() => {
                    isDragging = true;
                    draggedProjectId = activeCard.dataset.projectId;
                    activeCard.style.opacity = '0.5';

                    const rect = activeCard.getBoundingClientRect();
                    ghostCard = activeCard.cloneNode(true);
                    ghostCard.style.position = 'fixed';
                    ghostCard.style.width = rect.width + 'px';
                    ghostCard.style.height = rect.height + 'px';
                    ghostCard.style.top = rect.top + 'px';
                    ghostCard.style.left = rect.left + 'px';
                    ghostCard.style.zIndex = '1000';
                    ghostCard.style.opacity = '0.9';
                    ghostCard.style.pointerEvents = 'none';
                    ghostCard.style.transform = 'scale(1.02)';
                    ghostCard.style.boxShadow = '0 10px 25px rgba(0,0,0,0.2)';
                    document.body.appendChild(ghostCard);

                    if (navigator.vibrate) navigator.vibrate(20);
                }, LONG_PRESS_MS);
            }, { passive: true });

            card.addEventListener('touchmove', function(e) {
                const touch = e.touches[0];
                if (!touchStartPos) return;

                const dx = Math.abs(touch.clientX - touchStartPos.x);
                const dy = Math.abs(touch.clientY - touchStartPos.y);

                if (!isDragging && (dx > MOVE_THRESHOLD || dy > MOVE_THRESHOLD)) {
                    clearTimeout(longPressTimer);
                    longPressTimer = null;
                    touchStartPos = null;
                    activeCard = null;
                    return;
                }

                if (isDragging && ghostCard) {
                    e.preventDefault();
                    const rect = activeCard.getBoundingClientRect();
                    ghostCard.style.top = (touch.clientY - rect.height / 2) + 'px';
                    ghostCard.style.left = (touch.clientX - rect.width / 2) + 'px';

                    columns.forEach(col => col.classList.remove('ring-2', 'ring-accent/30'));
                    const target = getColumnAtPoint(touch.clientX, touch.clientY);
                    if (target) {
                        target.classList.add('ring-2', 'ring-accent/30');
                    }
                    updateScroll(touch.clientY);
                }
            }, { passive: false });

            card.addEventListener('touchend', function(e) {
                clearTimeout(longPressTimer);
                longPressTimer = null;
                touchStartPos = null;

                if (isDragging && ghostCard) {
                    const touch = e.changedTouches[0];
                    const target = getColumnAtPoint(touch.clientX, touch.clientY);
                    if (target && draggedProjectId) {
                        const newStatus = target.dataset.status;
                        updateProjectStatus(draggedProjectId, newStatus);
                    }
                }

                endDrag();
            });

            card.addEventListener('touchcancel', function() {
                clearTimeout(longPressTimer);
                endDrag();
            });
        });
    })();
</script>
@endpush
@endsection
