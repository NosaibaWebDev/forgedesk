@extends('layouts.app')

@section('title', $project->title . ' - מנהל')

@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm">
    <a href="{{ route('admin.dashboard') }}" class="text-ink-muted hover:text-accent transition">בית</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <a href="{{ route('admin.projects.index') }}" class="text-ink-muted hover:text-accent transition">פרויקטים</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <span class="text-ink font-medium">{{ $project->title }}</span>
</nav>
@endsection

@section('actions')
<div class="flex items-center gap-3">
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" @click.outside="open = false" class="btn-ghost inline-flex items-center gap-2">
            <i data-lucide="download" class="w-4 h-4"></i>
            ייצוא
            <i data-lucide="chevron-down" class="w-3 h-3"></i>
        </button>
        <div x-show="open" x-transition x-cloak class="absolute left-0 mt-2 w-48 shadow-elevated z-50 py-1 overflow-hidden" style="background:var(--color-card); border:1px solid var(--color-border); border-radius:12px;">
            <a href="{{ route('admin.projects.export.project.csv', $project) }}" class="dropdown-item"><i data-lucide="file-text"></i>ייצוא CSV</a>
            <a href="{{ route('admin.projects.export.project.pdf', $project) }}" class="dropdown-item" target="_blank"><i data-lucide="file"></i>ייצוא PDF</a>
        </div>
    </div>
    <a href="{{ route('admin.projects.edit', $project) }}" class="btn-secondary">
        ערוך
    </a>
    <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" onsubmit="return confirm('האם אתה בטוח שברצונך למחוק פרויקט זה?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-danger">
            מחק
        </button>
    </form>
</div>
@endsection

@section('content')
<div class="space-y-6">
    {{-- Project Info --}}
    <div class="bg-white rounded-card border border-border shadow-card p-6">
        <h3 class="text-lg font-semibold text-ink mb-5">פרטי הפרויקט</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-ink-muted mb-1">לקוח</p>
                <a href="{{ route('admin.clients.show', $project->user) }}" class="text-accent hover:underline font-medium">
                    {{ $project->user->name }}
                </a>
            </div>
            <div>
                <p class="text-sm text-ink-muted mb-1">סטטוס</p>
                @php
                    $statusColors = [
                        'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                        'in_progress' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                        'review' => 'bg-violet-50 text-violet-700 ring-violet-600/20',
                        'completed' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                        'cancelled' => 'bg-red-50 text-red-700 ring-red-600/20',
                    ];
                @endphp
                <span class="rounded-badge px-3 py-1 text-xs font-medium ring-1 ring-inset {{ $statusColors[$project->status] ?? '' }}">
                    {{ $project->status_label }}
                </span>
            </div>
            <div>
                <p class="text-sm text-ink-muted mb-1">עדיפות</p>
                <span class="font-medium text-ink">{{ $project->priority_label }}</span>
            </div>
            <div>
                <p class="text-sm text-ink-muted mb-1">תקציב</p>
                <span class="font-medium text-ink">₪{{ number_format($project->budget ?? 0, 0) }}</span>
            </div>
            <div>
                <p class="text-sm text-ink-muted mb-1">שולם</p>
                <span class="font-medium text-ink">₪{{ number_format($project->paid_amount, 0) }}</span>
            </div>
            <div>
                <p class="text-sm text-ink-muted mb-1">התקדמות</p>
                <div class="flex items-center gap-3 mt-1">
                    <div class="flex-1 bg-gray-100 rounded-full h-2 w-32">
                        <div class="bg-accent h-2 rounded-full transition-all" style="width: {{ $project->progress }}%"></div>
                    </div>
                    <span class="text-sm font-medium text-ink">{{ $project->progress }}%</span>
                </div>
            </div>
            <div>
                <p class="text-sm text-ink-muted mb-1">תאריך התחלה</p>
                <span class="font-medium text-ink">{{ $project->start_date?->format('d/m/Y') ?? '-' }}</span>
            </div>
            <div>
                <p class="text-sm text-ink-muted mb-1">תאריך יעד</p>
                <span class="font-medium text-ink">{{ $project->due_date?->format('d/m/Y') ?? '-' }}</span>
            </div>
            <div>
                <p class="text-sm text-ink-muted mb-1">הושלם ב-</p>
                <span class="font-medium text-ink">{{ $project->completed_at?->format('d/m/Y') ?? '-' }}</span>
            </div>
        </div>
        @if($project->description)
            <div class="mt-5 pt-5 border-t border-border">
                <p class="text-sm text-ink-muted mb-1.5">תיאור</p>
                <p class="text-ink-secondary">{{ $project->description }}</p>
            </div>
        @endif
        @if($project->notes)
            <div class="mt-5 pt-5 border-t border-border">
                <p class="text-sm text-ink-muted mb-1.5">הערות</p>
                <p class="text-ink-secondary">{{ $project->notes }}</p>
            </div>
        @endif
    </div>

    {{-- Tasks --}}
    <div class="bg-white rounded-card border border-border shadow-card p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-semibold text-ink">משימות</h3>
            <button onclick="document.getElementById('add-task-form').classList.toggle('hidden')" class="btn-primary">
                + משימה חדשה
            </button>
        </div>

        <div id="add-task-form" class="hidden mb-6 p-5 bg-surface rounded-card border border-border">
            <form method="POST" action="{{ route('admin.projects.tasks.store', $project) }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="task_title" class="label">כותרת *</label>
                        <input type="text" id="task_title" name="title" required
                            class="input" placeholder="כותרת המשימה">
                    </div>
                    <div>
                        <label for="task_assigned_to" class="label">מוקצה ל</label>
                        <input type="text" id="task_assigned_to" name="assigned_to" placeholder="מזהה משתמש"
                            class="input">
                    </div>
                    <div>
                        <label for="task_status" class="label">סטטוס *</label>
                        <select id="task_status" name="status" required
                            class="input">
                            <option value="pending">ממתין</option>
                            <option value="in_progress">בתהליך</option>
                            <option value="review">בבדיקה</option>
                            <option value="completed">הושלם</option>
                        </select>
                    </div>
                    <div>
                        <label for="task_priority" class="label">עדיפות *</label>
                        <select id="task_priority" name="priority" required
                            class="input">
                            <option value="low">נמוכה</option>
                            <option value="medium" selected>בינונית</option>
                            <option value="high">גבוהה</option>
                            <option value="urgent">דחופה</option>
                        </select>
                    </div>
                    <div>
                        <label for="task_estimated_hours" class="label">שעות מוערכות</label>
                        <input type="number" id="task_estimated_hours" name="estimated_hours" min="0" step="0.5"
                            class="input">
                    </div>
                    <div>
                        <label for="task_due_date" class="label">תאריך יעד</label>
                        <input type="date" id="task_due_date" name="due_date"
                            class="input">
                    </div>
                </div>
                <div class="mb-4">
                    <label for="task_description" class="label">תיאור</label>
                    <textarea id="task_description" name="description" rows="2"
                        class="input"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="btn-primary">
                        הוסף משימה
                    </button>
                    <button type="button" onclick="document.getElementById('add-task-form').classList.add('hidden')" class="btn-ghost">
                        ביטול
                    </button>
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead>
                    <tr class="border-b border-border">
                        <th class="px-5 py-3.5 text-xs font-semibold text-ink-muted uppercase tracking-wider">כותרת</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-ink-muted uppercase tracking-wider">סטטוס</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-ink-muted uppercase tracking-wider">עדיפות</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-ink-muted uppercase tracking-wider">מוקצה ל</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-ink-muted uppercase tracking-wider">שעות מוערכות</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-ink-muted uppercase tracking-wider">תאריך יעד</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-ink-muted uppercase tracking-wider">פעולות</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($project->tasks as $task)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4 text-sm font-medium text-ink">{{ $task->title }}</td>
                            <td class="px-5 py-4">
                                @php
                                    $taskStatusColors = [
                                        'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                                        'in_progress' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                                        'review' => 'bg-violet-50 text-violet-700 ring-violet-600/20',
                                        'completed' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                                    ];
                                @endphp
                                <form method="POST" action="{{ route('admin.projects.tasks.toggle', [$project, $task]) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="rounded-badge px-3 py-1 text-xs font-medium ring-1 ring-inset {{ $taskStatusColors[$task->status] ?? '' }} hover:opacity-80 transition cursor-pointer" title="לחץ להחלפת סטטוס">
                                        {{ $task->status_label }} →
                                    </button>
                                </form>
                            </td>
                            <td class="px-5 py-4 text-sm text-ink-secondary">{{ $task->priority_label }}</td>
                            <td class="px-5 py-4 text-sm text-ink-secondary">{{ $task->assignee?->name ?? '-' }}</td>
                            <td class="px-5 py-4 text-sm text-ink-secondary">{{ $task->estimated_hours ?? '-' }}</td>
                            <td class="px-5 py-4 text-sm text-ink-secondary">{{ $task->due_date?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <button onclick="toggleEditTask({{ $task->id }})" class="text-ink-secondary hover:text-ink text-sm font-medium transition">ערוך</button>
                                    <button onclick="toggleTaskImages({{ $task->id }})" class="text-ink-secondary hover:text-ink text-sm font-medium transition">תמונות</button>
                                    <form method="POST" action="{{ route('admin.projects.tasks.destroy', [$project, $task]) }}" onsubmit="return confirm('האם אתה בטוח?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-danger hover:text-red-600 text-sm font-medium transition">מחק</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <tr id="task-images-{{ $task->id }}" class="hidden">
                            <td colspan="7" class="px-5 py-5 bg-surface">
                                <div class="space-y-4">
                                    @if($task->images->count())
                                        <div class="flex flex-wrap gap-3">
                                            @foreach($task->images as $image)
                                                <div class="relative group">
                                                    <a href="{{ $image->url }}" target="_blank" class="block">
                                                        <img src="{{ $image->url }}" alt="{{ $image->original_name }}" width="96" height="96" class="w-24 h-24 rounded-btn object-cover border border-border">
                                                    </a>
                                                    <form method="POST" action="{{ route('admin.projects.tasks.images.destroy', [$project, $task, $image]) }}" onsubmit="return confirm('האם אתה בטוח?')" class="absolute top-1 left-1 opacity-0 group-hover:opacity-100 transition">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="w-6 h-6 rounded-full bg-danger text-white flex items-center justify-center text-xs shadow-sm hover:bg-red-600 transition">×</button>
                                                    </form>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                    <form method="POST" action="{{ route('admin.projects.tasks.images.store', [$project, $task]) }}" enctype="multipart/form-data" class="flex items-end gap-3">
                                        @csrf
                                        <div class="flex-1 min-w-0">
                                            <label class="label text-xs">העלאת תמונות למשימה (עד 5 קבצים, 5MB כל אחד)</label>
                                            <input type="file" name="images[]" multiple accept="image/*" required class="input file:ml-0 file:py-2 file:px-4 file:rounded-btn file:border-0 file:text-sm file:font-medium file:bg-accent file:text-white file:hover:bg-accent-dark file:cursor-pointer file:transition">
                                        </div>
                                        <button type="submit" class="btn-primary whitespace-nowrap self-end">העלה</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <tr id="edit-task-{{ $task->id }}" class="hidden">
                            <td colspan="7" class="px-5 py-5 bg-surface">
                                <form method="POST" action="{{ route('admin.projects.tasks.update', [$project, $task]) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-3">
                                        <input type="text" name="title" value="{{ $task->title }}" required
                                            class="input" placeholder="כותרת">
                                        <select name="status" class="input">
                                            <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>ממתין</option>
                                            <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>בתהליך</option>
                                            <option value="review" {{ $task->status === 'review' ? 'selected' : '' }}>בבדיקה</option>
                                            <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>הושלם</option>
                                        </select>
                                        <select name="priority" class="input">
                                            <option value="low" {{ $task->priority === 'low' ? 'selected' : '' }}>נמוכה</option>
                                            <option value="medium" {{ $task->priority === 'medium' ? 'selected' : '' }}>בינונית</option>
                                            <option value="high" {{ $task->priority === 'high' ? 'selected' : '' }}>גבוהה</option>
                                            <option value="urgent" {{ $task->priority === 'urgent' ? 'selected' : '' }}>דחופה</option>
                                        </select>
                                        <input type="number" name="actual_hours" value="{{ $task->actual_hours }}" min="0" step="0.5"
                                            class="input" placeholder="שעות בפועל">
                                    </div>
                                    <textarea name="description" rows="2" class="input mb-3" placeholder="תיאור">{{ $task->description }}</textarea>
                                    <div class="flex gap-3">
                                        <button type="submit" class="btn-primary px-4 py-2">עדכן</button>
                                        <button type="button" onclick="toggleEditTask({{ $task->id }})" class="btn-ghost">ביטול</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <i data-lucide="list-checks" class="w-8 h-8 text-ink-muted/40"></i>
                                    <p class="text-ink-muted text-sm">אין משימות עדיין.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Files --}}
    <div class="bg-white rounded-card border border-border shadow-card p-6">
        <h3 class="text-lg font-semibold text-ink mb-5">קבצים</h3>

        <form method="POST" action="{{ route('admin.projects.files.store', $project) }}" enctype="multipart/form-data" class="mb-6 p-5 bg-surface rounded-card border border-border">
            @csrf
            <div class="flex items-end gap-3">
                <div class="flex-1 min-w-0">
                    <label for="admin_file" class="label">בחר קבצים</label>
                    <input type="file" id="admin_file" name="files[]" required
                        class="input file:ml-0 file:py-2 file:px-4 file:rounded-btn file:border-0 file:text-sm file:font-medium file:bg-accent file:text-white file:hover:bg-accent-dark file:cursor-pointer file:transition"
                        accept=".jpg,.jpeg,.png,.gif,.bmp,.webp,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar,.7z">
                </div>
                <div class="flex-1 min-w-0">
                    <label for="admin_file_notes" class="label">הערות</label>
                    <input type="text" id="admin_file_notes" name="notes"
                        class="input">
                </div>
                <button type="submit" class="btn-primary whitespace-nowrap self-end">
                    העלה קבצים
                </button>
            </div>
            <p class="text-xs text-ink-muted mt-2">מקסימום 10 קבצים, כל אחד עד 10MB.</p>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead>
                    <tr class="border-b border-border">
                        <th class="px-5 py-3.5 text-xs font-semibold text-ink-muted uppercase w-12"></th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-ink-muted uppercase tracking-wider">שם קובץ</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-ink-muted uppercase tracking-wider">גודל</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-ink-muted uppercase tracking-wider">הועלה על ידי</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-ink-muted uppercase tracking-wider">תאריך</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-ink-muted uppercase tracking-wider">פעולות</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($project->files as $file)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-4">
                                @if($file->isImage())
                                    <a href="{{ $file->getTemporaryUrl() }}" target="_blank" class="block">
                                        <img src="{{ $file->getTemporaryUrl() }}" alt="{{ $file->original_name }}" width="40" height="40" class="w-10 h-10 rounded-btn object-cover border border-border">
                                    </a>
                                @else
                                    <div class="w-10 h-10 bg-surface rounded-btn flex items-center justify-center border border-border">
                                        <i data-lucide="file" class="w-5 h-5 text-ink-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-sm font-medium">
                                <a href="{{ $file->getTemporaryUrl() }}" target="_blank" class="text-accent hover:underline">{{ $file->original_name }}</a>
                            </td>
                            <td class="px-5 py-4 text-sm text-ink-secondary">{{ $file->formatted_size }}</td>
                            <td class="px-5 py-4 text-sm text-ink-secondary">{{ $file->uploader->name ?? '-' }}</td>
                            <td class="px-5 py-4 text-sm text-ink-secondary">{{ $file->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-5 py-4">
                                <form method="POST" action="{{ route('admin.projects.files.destroy', [$project, $file]) }}" onsubmit="return confirm('האם אתה בטוח שברצונך למחוק קובץ זה?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-danger hover:text-red-600 text-sm font-medium transition">מחק</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <i data-lucide="paperclip" class="w-8 h-8 text-ink-muted/40"></i>
                                    <p class="text-ink-muted text-sm">אין קבצים עדיין.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Messages --}}
    <div class="bg-white rounded-card border border-border shadow-card p-6">
        <h3 class="text-lg font-semibold text-ink mb-5">הודעות</h3>

        <form method="POST" action="{{ route('admin.messages.store', $project) }}" class="mb-6">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $project->user_id }}">
            <div class="flex gap-3">
                <input type="text" name="body" required placeholder="הקלד הודעה..."
                    class="input flex-1">
                <button type="submit" class="btn-primary px-6 py-3">
                    שלח
                </button>
            </div>
            @error('body') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
        </form>

        <div class="space-y-4 max-h-96 overflow-y-auto">
            @forelse($project->messages->sortBy('created_at') as $message)
                <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-start' : 'justify-end' }}">
                    <div class="max-w-md px-5 py-3.5 rounded-2xl {{ $message->sender_id === auth()->id() ? 'bg-accent/10 text-ink border border-accent/20' : 'bg-white text-ink border border-border shadow-sm' }}">
                        <p class="text-xs text-ink-muted mb-1">{{ $message->sender->name }} · {{ $message->created_at->format('d/m/Y H:i') }}</p>
                        <p class="text-sm leading-relaxed">{{ $message->body }}</p>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <i data-lucide="message-circle" class="w-8 h-8 text-ink-muted/40 mx-auto mb-2"></i>
                    <p class="text-ink-muted text-sm">אין הודעות עדיין.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleEditTask(taskId) {
        const row = document.getElementById('edit-task-' + taskId);
        row.classList.toggle('hidden');
    }

    function toggleTaskImages(taskId) {
        const row = document.getElementById('task-images-' + taskId);
        row.classList.toggle('hidden');
    }
</script>
@endpush
@endsection
