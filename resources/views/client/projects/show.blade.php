@extends('layouts.app')

@section('title', $project->title . ' - פרויקט')
@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm">
    <a href="{{ route('client.dashboard') }}" class="text-ink-muted hover:text-accent transition">בית</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <a href="{{ route('client.projects.index') }}" class="text-ink-muted hover:text-accent transition">הפרויקטים שלי</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <span class="text-ink font-medium">{{ $project->title }}</span>
</nav>
@endsection

@section('actions')
<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" @click.outside="open = false" class="btn-ghost inline-flex items-center gap-2">
        <i data-lucide="download" class="w-4 h-4"></i>
        ייצוא
        <i data-lucide="chevron-down" class="w-3 h-3"></i>
    </button>
    <div x-show="open" x-transition x-cloak class="absolute left-0 mt-2 w-48 shadow-elevated z-50 py-1 overflow-hidden" style="background:var(--color-card); border:1px solid var(--color-border); border-radius:12px;">
        <a href="{{ route('client.projects.export.project.csv', $project) }}" class="dropdown-item"><i data-lucide="file-text"></i>ייצוא CSV</a>
    </div>
</div>
@endsection

@section('content')
<div class="space-y-6">
    {{-- Project Info --}}
    <div class="bg-white rounded-card border border-border shadow-card p-6">
        <h3 class="text-lg font-semibold text-ink mb-5">פרטי הפרויקט</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="label">סטטוס</label>
                @php
                    $statusColors = [
                        'pending' => 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20',
                        'in_progress' => 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20',
                        'review' => 'bg-violet-50 text-violet-700 ring-1 ring-inset ring-violet-600/20',
                        'completed' => 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20',
                        'cancelled' => 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20',
                    ];
                @endphp
                <span class="rounded-badge px-3 py-1 text-xs font-medium ring-1 ring-inset {{ $statusColors[$project->status] ?? '' }}">
                    {{ $project->status_label }}
                </span>
            </div>
            <div>
                <label class="label">עדיפות</label>
                @php
                    $priorityColors = [
                        'low' => 'text-ink-muted',
                        'medium' => 'text-amber-600',
                        'high' => 'text-orange-600',
                        'urgent' => 'text-danger',
                    ];
                @endphp
                <span class="font-medium {{ $priorityColors[$project->priority] ?? '' }}">{{ $project->priority_label }}</span>
            </div>
            <div>
                <label class="label">התקדמות</label>
                <div class="flex items-center gap-2.5 mt-1">
                    <div class="flex-1 bg-accent/10 rounded-full h-2">
                        <div class="bg-accent h-2 rounded-full transition-all" style="width: {{ $project->progress }}%"></div>
                    </div>
                    <span class="text-sm font-medium text-ink-secondary">{{ $project->progress }}%</span>
                </div>
            </div>
            <div>
                <label class="label">תקציב</label>
                <span class="font-medium text-ink">₪{{ number_format($project->budget ?? 0, 0) }}</span>
            </div>
            <div>
                <label class="label">שולם</label>
                <span class="font-medium text-ink">₪{{ number_format($project->paid_amount, 0) }}</span>
            </div>
            <div>
                <label class="label">יתרה</label>
                <span class="font-medium {{ ($project->budget - $project->paid_amount) > 0 ? 'text-danger' : 'text-green-600' }}">
                    ₪{{ number_format(($project->budget ?? 0) - $project->paid_amount, 0) }}
                </span>
            </div>
            <div>
                <label class="label">תאריך התחלה</label>
                <span class="font-medium text-ink">{{ $project->start_date?->format('d/m/Y') ?? '-' }}</span>
            </div>
            <div>
                <label class="label">תאריך יעד</label>
                <span class="font-medium text-ink">{{ $project->due_date?->format('d/m/Y') ?? '-' }}</span>
            </div>
            <div>
                <label class="label">הושלם ב</label>
                <span class="font-medium text-ink">{{ $project->completed_at?->format('d/m/Y') ?? '-' }}</span>
            </div>
        </div>
        @if($project->description)
            <div class="mt-5 pt-5 border-t border-border">
                <label class="label">תיאור</label>
                <p class="text-ink-secondary">{{ $project->description }}</p>
            </div>
        @endif
    </div>

    {{-- Tasks --}}
    <div class="bg-white rounded-card border border-border shadow-card p-6">
        <h3 class="text-lg font-semibold text-ink mb-5">משימות</h3>
        @if($project->tasks->count())
            <div class="space-y-3">
                @foreach($project->tasks as $task)
                    <div class="p-4 bg-gray-50/50 rounded-btn hover:bg-gray-100/50 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4 min-w-0">
                                <div>
                                    @if($task->status === 'completed')
                                        <div class="w-6 h-6 bg-accent rounded-full flex items-center justify-center">
                                            <i data-lucide="check" class="w-4 h-4 text-white"></i>
                                        </div>
                                    @else
                                        <div class="w-6 h-6 border-2 border-border rounded-full"></div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="font-medium text-ink truncate {{ $task->status === 'completed' ? 'line-through text-ink-muted' : '' }}">{{ $task->title }}</p>
                                    @if($task->description)
                                        <p class="text-sm text-ink-muted mt-1">{{ $task->description }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                @php
                                    $taskStatusColors = [
                                        'pending' => 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20',
                                        'in_progress' => 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20',
                                        'review' => 'bg-violet-50 text-violet-700 ring-1 ring-inset ring-violet-600/20',
                                        'completed' => 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20',
                                    ];
                                @endphp
                                <span class="rounded-badge px-3 py-1 text-xs font-medium ring-1 ring-inset {{ $taskStatusColors[$task->status] ?? '' }}">
                                    {{ $task->status_label }}
                                </span>
                                <span class="text-xs text-ink-muted">{{ $task->priority_label }}</span>
                                <button onclick="document.getElementById('client-task-images-{{ $task->id }}').classList.toggle('hidden')" class="text-xs text-accent hover:text-accent-dark font-medium transition flex items-center gap-1">
                                    <i data-lucide="image" class="w-4 h-4"></i> תמונות
                                </button>
                            </div>
                        </div>

                        <div id="client-task-images-{{ $task->id }}" class="hidden mt-3 pt-3 border-t border-border space-y-3">
                            @if($task->images->count())
                                <div class="flex flex-wrap gap-2">
                                    @foreach($task->images as $image)
                                        <div class="relative group">
                                            <a href="{{ $image->url }}" target="_blank" class="block">
                                                <img src="{{ $image->url }}" alt="{{ $image->original_name }}" width="80" height="80" class="w-20 h-20 rounded-btn object-cover border border-border">
                                            </a>
                                            <form method="POST" action="{{ route('client.projects.tasks.images.destroy', [$project, $task, $image]) }}" onsubmit="return confirm('האם אתה בטוח?')" class="absolute top-0.5 right-0.5 opacity-0 group-hover:opacity-100 transition">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-5 h-5 rounded-full bg-danger text-white flex items-center justify-center text-[10px] shadow-sm hover:bg-red-600 transition">×</button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            <form method="POST" action="{{ route('client.projects.tasks.images.store', [$project, $task]) }}" enctype="multipart/form-data" class="flex items-end gap-3">
                                @csrf
                                <div class="flex-1 min-w-0">
                                    <input type="file" name="images[]" multiple accept="image/*" required class="file-input">
                                    <p class="text-[10px] text-ink-muted mt-1">עד 5 קבצים, 5MB כל אחד</p>
                                </div>
                                <button type="submit" class="btn-primary btn-sm whitespace-nowrap">העלה</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-ink-muted py-8">אין משימות עדיין.</p>
        @endif
    </div>

    {{-- Files --}}
    <div class="bg-white rounded-card border border-border shadow-card p-6">
        <h3 class="text-lg font-semibold text-ink mb-5">קבצים</h3>

        <form method="POST" action="{{ route('client.projects.files.store', $project) }}" enctype="multipart/form-data" class="mb-6 p-4 bg-gray-50/50 rounded-btn border border-border">
            @csrf
            <div class="flex items-end gap-3">
                <div class="flex-1 min-w-0">
                    <label for="file" class="label">בחר קבצים</label>
                    <input type="file" id="file" name="files[]" multiple required
                        class="file-input"
                        accept=".jpg,.jpeg,.png,.gif,.bmp,.webp,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar,.7z">
                </div>
                <div class="flex-1 min-w-0">
                    <label for="file_notes" class="label">הערות</label>
                    <input type="text" id="file_notes" name="notes"
                        class="input text-right">
                </div>
                <button type="submit" class="btn-primary whitespace-nowrap h-[44px]">
                    העלה קבצים
                </button>
            </div>
            <p class="text-xs text-ink-muted mt-2">מקסימום 10 קבצים, כל אחד עד 10MB.</p>
        </form>

        @if($project->files->count())
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($project->files as $file)
                    <div class="flex items-center justify-between p-4 bg-gray-50/50 rounded-btn border border-border/50 hover:bg-gray-100/50 transition">
                        <div class="flex items-center gap-3 min-w-0">
                            @if($file->isImage())
                                <a href="{{ $file->getTemporaryUrl() }}" target="_blank" class="flex-shrink-0">
                                    <img src="{{ $file->getTemporaryUrl() }}" alt="{{ $file->original_name }}" width="40" height="40" class="w-10 h-10 rounded-btn object-cover border border-border">
                                </a>
                            @else
                                <div class="w-10 h-10 bg-accent/10 rounded-btn flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="file" class="w-5 h-5 text-accent"></i>
                                </div>
                            @endif
                            <div class="min-w-0">
                                <a href="{{ $file->getTemporaryUrl() }}" target="_blank" class="text-sm font-medium text-accent hover:text-accent-dark truncate block transition">{{ $file->original_name }}</a>
                                <p class="text-xs text-ink-muted">{{ $file->formatted_size }} - {{ $file->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('client.projects.files.destroy', [$project, $file]) }}" onsubmit="return confirm('האם אתה בטוח?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-danger hover:text-red-600 text-sm flex-shrink-0 font-medium transition">מחק</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-ink-muted py-8">אין קבצים עדיין.</p>
        @endif
    </div>

    {{-- Messages --}}
    <div class="bg-white rounded-card border border-border shadow-card p-6">
        <h3 class="text-lg font-semibold text-ink mb-5">הודעות</h3>

        <form method="POST" action="{{ route('client.messages.store', $project) }}" class="mb-6">
            @csrf
            <div class="flex gap-3">
                <input type="text" name="body" required placeholder="הקלד הודעה..."
                    class="input flex-1">
                <button type="submit" class="btn-primary">
                    שלח
                </button>
            </div>
            @error('body') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
        </form>

        <div id="messages-container" class="space-y-4 max-h-96 overflow-y-auto">
            @forelse($project->messages as $message)
                <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-start' : 'justify-end' }}">
                    <div class="{{ $message->sender_id === auth()->id() ? 'max-w-md px-5 py-3.5 rounded-2xl bg-accent/10 text-ink border border-accent/20' : 'max-w-md px-5 py-3.5 rounded-2xl bg-white text-ink border border-border shadow-sm' }}">
                        <p class="text-xs text-ink-muted mb-1">{{ $message->sender->name }} · {{ $message->created_at->format('d/m/Y H:i') }}</p>
                        <p class="text-sm leading-relaxed">{{ $message->body }}</p>
                    </div>
                </div>
            @empty
                <p class="text-center text-ink-muted py-6">אין הודעות עדיין. שלח הודעה ראשונה!</p>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
    const container = document.getElementById('messages-container');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
</script>
@endpush
@endsection
