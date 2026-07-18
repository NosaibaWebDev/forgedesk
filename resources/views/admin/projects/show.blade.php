@extends('layouts.app')

@section('title', $project->title . ' - ' . __('admin_dashboard'))

@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm">
    <a href="{{ route('admin.dashboard') }}" class="text-ink-muted hover:text-accent transition">{{ __('home') }}</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <a href="{{ route('admin.projects.index') }}" class="text-ink-muted hover:text-accent transition">{{ __('projects') }}</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <span class="text-ink font-medium">{{ $project->title }}</span>
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
            <a href="{{ route('admin.projects.export.project.csv', $project) }}" class="dropdown-item"><i data-lucide="file-text"></i>{{ __('export_csv') }}</a>
            <a href="{{ route('admin.projects.export.project.pdf', $project) }}" class="dropdown-item" target="_blank"><i data-lucide="file"></i>{{ __('export_pdf') }}</a>
        </div>
    </div>
    <a href="{{ route('admin.projects.edit', $project) }}" class="btn-secondary">
        {{ __('edit_button') }}
    </a>
    <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" onsubmit="return confirm('{{ __("confirm_delete_project") }}')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-danger">
            {{ __('delete') }}
        </button>
    </form>
</div>
@endsection

@section('content')
<div class="space-y-6">
    {{-- Project Info --}}
    <div class="bg-white rounded-card border border-border shadow-card p-6">
        <h3 class="text-lg font-semibold text-ink mb-5">{{ __('project_details') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-ink-muted mb-1">{{ __('client') }}</p>
                <a href="{{ route('admin.clients.show', $project->user) }}" class="text-accent hover:underline font-medium">
                    {{ $project->user->name }}
                </a>
            </div>
            <div>
                <p class="text-sm text-ink-muted mb-1">{{ __('status') }}</p>
                @php
                    $statusColors = [
                        'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                        'in_progress' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                        'review' => 'bg-violet-50 text-violet-700 ring-violet-600/20',
                        'completed' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                        'cancelled' => 'bg-red-50 text-red-700 ring-red-600/20',
                    ];
                @endphp
                <div x-data="{ open: false }" class="relative inline-block">
                    <button @click="open = !open" @click.outside="open = false"
                            class="inline-flex items-center gap-1.5 rounded-badge px-3 py-1 text-xs font-medium ring-1 ring-inset cursor-pointer hover:opacity-80 transition {{ $statusColors[$project->status->value] ?? '' }}">
                        {{ $project->status_label }}
                        <i data-lucide="chevron-down" class="w-3 h-3"></i>
                    </button>
                    <div x-show="open" x-transition x-cloak
                         class="absolute top-full mt-0.5 right-0 w-44 rounded-card py-1 z-50 shadow-elevated"
                         style="background:var(--color-card); border:1px solid var(--color-border);">
                        @foreach(\App\Enums\ProjectStatus::cases() as $s)
                            @if($s->value !== $project->status->value)
                                <button onclick="changeProjectStatus({{ $project->id }}, '{{ $s->value }}', this)"
                                        class="w-full text-left px-4 py-2 text-sm flex items-center gap-2 hover:bg-gray-50 transition"
                                        style="color:var(--color-ink);">
                                    <span class="w-2 h-2 rounded-full {{ str_replace(['ring-600/20', 'text-'], ['', 'bg-'], $statusColors[$s->value] ?? '') }}"></span>
                                    {{ $s->label() }}
                                </button>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            <div>
                <p class="text-sm text-ink-muted mb-1">{{ __('priority') }}</p>
                <span class="font-medium text-ink">{{ $project->priority_label }}</span>
            </div>
            <div>
                <p class="text-sm text-ink-muted mb-1">{{ __('budget') }}</p>
                <span class="font-medium text-ink">₪{{ number_format($project->budget ?? 0, 0) }}</span>
            </div>
            <div>
                <p class="text-sm text-ink-muted mb-1">{{ __('paid_amount') }}</p>
                <span class="font-medium text-ink">₪{{ number_format($project->paid_amount, 0) }}</span>
            </div>
            <div>
                <p class="text-sm text-ink-muted mb-1">{{ __('progress') }}</p>
                <div class="flex items-center gap-3 mt-1">
                    <div class="flex-1 bg-gray-100 rounded-full h-2 w-32">
                        <div class="bg-accent h-2 rounded-full transition-all" style="width: {{ $project->progress }}%"></div>
                    </div>
                    <span class="text-sm font-medium text-ink">{{ $project->progress }}%</span>
                </div>
            </div>
            <div>
                <p class="text-sm text-ink-muted mb-1">{{ __('hourly_rate') }}</p>
                <span class="font-medium text-ink">{{ $project->hourly_rate !== null ? '₪' . number_format($project->hourly_rate, 2) : '-' }}</span>
            </div>
            <div>
                <p class="text-sm text-ink-muted mb-1">{{ __('estimated_hours') }}</p>
                <span class="font-medium text-ink">{{ $project->estimated_hours ?? '-' }}</span>
            </div>
            <div>
                <p class="text-sm text-ink-muted mb-1">{{ __('estimated_total') }}</p>
                <span class="font-semibold text-accent">{{ $project->total_price !== null ? '₪' . number_format($project->total_price, 2) : '-' }}</span>
            </div>
            <div>
                <p class="text-sm text-ink-muted mb-1">{{ __('start_date') }}</p>
                <span class="font-medium text-ink">{{ $project->start_date?->format('d/m/Y') ?? '-' }}</span>
            </div>
            <div>
                <p class="text-sm text-ink-muted mb-1">{{ __('due_date') }}</p>
                <span class="font-medium text-ink">{{ $project->due_date?->format('d/m/Y') ?? '-' }}</span>
            </div>
            <div>
                <p class="text-sm text-ink-muted mb-1">{{ __('completed_date') }}</p>
                <span class="font-medium text-ink">{{ $project->completed_at?->format('d/m/Y') ?? '-' }}</span>
            </div>
        </div>
        @if($project->description)
            <div class="mt-5 pt-5 border-t border-border">
                <p class="text-sm text-ink-muted mb-1.5">{{ __('description') }}</p>
                <p class="text-ink-secondary">{{ $project->description }}</p>
            </div>
        @endif
        @if($project->notes)
            <div class="mt-5 pt-5 border-t border-border">
                <p class="text-sm text-ink-muted mb-1.5">{{ __('notes') }}</p>
                <p class="text-ink-secondary">{{ $project->notes }}</p>
            </div>
        @endif
    </div>

    {{-- Tasks --}}
    <div class="bg-white rounded-card border border-border shadow-card p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-semibold text-ink">{{ __('tasks') }}</h3>
            <button onclick="document.getElementById('add-task-form').classList.toggle('hidden')" class="btn-primary">
                + {{ __('new_task') }}
            </button>
        </div>

        <div id="add-task-form" class="hidden mb-6 p-5 bg-surface rounded-card border border-border">
            <form method="POST" action="{{ route('admin.projects.tasks.store', $project) }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="task_title" class="label">{{ __('task_title') }} *</label>
                        <input type="text" id="task_title" name="title" required
                            class="input" placeholder="{{ __('enter_task_title') }}">
                    </div>
                    <div>
                        <label for="task_assigned_to" class="label">{{ __('assigned_to') }}</label>
                        <input type="text" id="task_assigned_to" name="assigned_to" placeholder="{{ __('user_id_placeholder') }}"
                            class="input">
                    </div>
                    <div>
                        <label for="task_status" class="label">{{ __('status') }} *</label>
                        <select id="task_status" name="status" required
                            class="input">
                            <option value="pending">{{ __('pending') }}</option>
                            <option value="in_progress">{{ __('in_progress') }}</option>
                            <option value="review">{{ __('review') }}</option>
                            <option value="completed">{{ __('completed') }}</option>
                        </select>
                    </div>
                    <div>
                        <label for="task_priority" class="label">{{ __('priority') }} *</label>
                        <select id="task_priority" name="priority" required
                            class="input">
                            <option value="low">{{ __('priority_low') }}</option>
                            <option value="medium" selected>{{ __('priority_medium') }}</option>
                            <option value="high">{{ __('priority_high') }}</option>
                            <option value="urgent">{{ __('priority_urgent') }}</option>
                        </select>
                    </div>
                    <div>
                        <label for="task_estimated_hours" class="label">{{ __('estimated_hours') }}</label>
                        <input type="number" id="task_estimated_hours" name="estimated_hours" min="0" step="0.5"
                            class="input">
                    </div>
                    <div>
                        <label for="task_due_date" class="label">{{ __('due_date') }}</label>
                        <input type="date" id="task_due_date" name="due_date"
                            class="input">
                    </div>
                </div>
                <div class="mb-4">
                    <label for="task_description" class="label">{{ __('description') }}</label>
                    <textarea id="task_description" name="description" rows="2"
                        class="input"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="btn-primary">
                        {{ __('add_task') }}
                    </button>
                    <button type="button" onclick="document.getElementById('add-task-form').classList.add('hidden')" class="btn-ghost">
                        {{ __('cancel') }}
                    </button>
                </div>
            </form>
        </div>

        <div>
            <table class="w-full text-right">
                <thead>
                    <tr class="border-b border-border">
                        <th class="px-5 py-3.5 text-xs font-semibold text-ink-muted uppercase tracking-wider">{{ __('task_title') }}</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-ink-muted uppercase tracking-wider">{{ __('status') }}</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-ink-muted uppercase tracking-wider">{{ __('priority') }}</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-ink-muted uppercase tracking-wider">{{ __('assigned_to') }}</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-ink-muted uppercase tracking-wider">{{ __('estimated_hours') }}</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-ink-muted uppercase tracking-wider">{{ __('due_date') }}</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-ink-muted uppercase tracking-wider">{{ __('actions') }}</th>
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
                                <div x-data="{ open: false }" class="relative inline-block">
                                    <button @click="open = !open" @click.outside="open = false"
                                            class="rounded-badge px-3 py-1 text-xs font-medium ring-1 ring-inset {{ $taskStatusColors[$task->status->value] ?? '' }} hover:opacity-80 transition cursor-pointer inline-flex items-center gap-1">
                                        {{ $task->status_label }}
                                        <i data-lucide="chevron-down" class="w-3 h-3"></i>
                                    </button>
                                    <div x-show="open" x-transition x-cloak
                                         class="absolute top-full mt-0.5 right-0 w-40 rounded-card py-1 z-50 shadow-elevated"
                                         style="background:var(--color-card); border:1px solid var(--color-border);">
                                        @foreach(\App\Enums\TaskStatus::cases() as $ts)
                                            @if($ts->value !== $task->status->value)
                                                <button onclick="changeTaskStatus({{ $project->id }}, {{ $task->id }}, '{{ $ts->value }}', this)"
                                                        class="w-full text-left px-3 py-1.5 text-xs flex items-center gap-2 hover:bg-gray-50 transition"
                                                        style="color:var(--color-ink);">
                                                    <span class="w-2 h-2 rounded-full {{ str_replace(['ring-600/20', 'text-'], ['', 'bg-'], $taskStatusColors[$ts->value] ?? '') }}"></span>
                                                    {{ $ts->label() }}
                                                </button>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm text-ink-secondary">{{ $task->priority_label }}</td>
                            <td class="px-5 py-4 text-sm text-ink-secondary">{{ $task->assignee?->name ?? '-' }}</td>
                            <td class="px-5 py-4 text-sm text-ink-secondary">{{ $task->estimated_hours ?? '-' }}</td>
                            <td class="px-5 py-4 text-sm text-ink-secondary">{{ $task->due_date?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <button onclick="toggleEditTask({{ $task->id }})" class="text-ink-secondary hover:text-ink text-sm font-medium transition">{{ __('edit') }}</button>
                                    <button onclick="toggleTaskImages({{ $task->id }})" class="text-ink-secondary hover:text-ink text-sm font-medium transition">{{ __('task_images') }}</button>
                                    <form method="POST" action="{{ route('admin.projects.tasks.destroy', [$project, $task]) }}" onsubmit="return confirm('{{ __("confirm_delete_task") }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-danger hover:text-red-600 text-sm font-medium transition">{{ __('delete') }}</button>
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
                                                    <form method="POST" action="{{ route('admin.projects.tasks.images.destroy', [$project, $task, $image]) }}" onsubmit="return confirm('{{ __("confirm_delete_task") }}')" class="absolute top-1 left-1 opacity-0 group-hover:opacity-100 transition">
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
                                            <label class="label text-xs">{{ __('upload_task_images') }}</label>
                                            <input type="file" name="images[]" multiple accept="image/*" required class="input file:ml-0 file:py-2 file:px-4 file:rounded-btn file:border-0 file:text-sm file:font-medium file:bg-accent file:text-white file:hover:bg-accent-dark file:cursor-pointer file:transition">
                                        </div>
                                        <button type="submit" class="btn-primary whitespace-nowrap self-end">{{ __('upload') }}</button>
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
                                            class="input" placeholder="{{ __('task_title') }}">
                                        <select name="status" class="input">
                                            <option value="pending" {{ $task->status->value === 'pending' ? 'selected' : '' }}>{{ __('pending') }}</option>
                                            <option value="in_progress" {{ $task->status->value === 'in_progress' ? 'selected' : '' }}>{{ __('in_progress') }}</option>
                                            <option value="review" {{ $task->status->value === 'review' ? 'selected' : '' }}>{{ __('review') }}</option>
                                            <option value="completed" {{ $task->status->value === 'completed' ? 'selected' : '' }}>{{ __('completed') }}</option>
                                        </select>
                                        <select name="priority" class="input">
                                            <option value="low" {{ $task->priority->value === 'low' ? 'selected' : '' }}>{{ __('priority_low') }}</option>
                                            <option value="medium" {{ $task->priority->value === 'medium' ? 'selected' : '' }}>{{ __('priority_medium') }}</option>
                                            <option value="high" {{ $task->priority->value === 'high' ? 'selected' : '' }}>{{ __('priority_high') }}</option>
                                            <option value="urgent" {{ $task->priority->value === 'urgent' ? 'selected' : '' }}>{{ __('priority_urgent') }}</option>
                                        </select>
                                        <input type="number" name="actual_hours" value="{{ $task->actual_hours }}" min="0" step="0.5"
                                            class="input" placeholder="{{ __('actual_hours') }}">
                                    </div>
                                    <textarea name="description" rows="2" class="input mb-3" placeholder="{{ __('description') }}">{{ $task->description }}</textarea>
                                    <div class="flex gap-3">
                                        <button type="submit" class="btn-primary px-4 py-2">{{ __('save') }}</button>
                                        <button type="button" onclick="toggleEditTask({{ $task->id }})" class="btn-ghost">{{ __('cancel') }}</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <i data-lucide="list-checks" class="w-8 h-8 text-ink-muted/40"></i>
                                    <p class="text-ink-muted text-sm">{{ __('no_tasks') }}</p>
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
        <h3 class="text-lg font-semibold text-ink mb-5">{{ __('files') }}</h3>

        <form method="POST" action="{{ route('admin.projects.files.store', $project) }}" enctype="multipart/form-data" class="mb-6 p-5 bg-surface rounded-card border border-border">
            @csrf
            <div class="flex items-end gap-3">
                <div class="flex-1 min-w-0">
                    <label for="admin_file" class="label">{{ __('choose_files') }}</label>
                    <input type="file" id="admin_file" name="files[]" required
                        class="input file:ml-0 file:py-2 file:px-4 file:rounded-btn file:border-0 file:text-sm file:font-medium file:bg-accent file:text-white file:hover:bg-accent-dark file:cursor-pointer file:transition"
                        accept=".jpg,.jpeg,.png,.gif,.bmp,.webp,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar,.7z">
                </div>
                <div class="flex-1 min-w-0">
                    <label for="admin_file_notes" class="label">{{ __('notes') }}</label>
                    <input type="text" id="admin_file_notes" name="notes"
                        class="input">
                </div>
                <button type="submit" class="btn-primary whitespace-nowrap self-end">
                    {{ __('upload_files') }}
                </button>
            </div>
            <p class="text-xs text-ink-muted mt-2">{{ __('max_files_helper') }}</p>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead>
                    <tr class="border-b border-border">
                        <th class="px-5 py-3.5 text-xs font-semibold text-ink-muted uppercase w-12"></th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-ink-muted uppercase tracking-wider">{{ __('file_name') }}</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-ink-muted uppercase tracking-wider">{{ __('file_size') }}</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-ink-muted uppercase tracking-wider">{{ __('uploaded_by') }}</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-ink-muted uppercase tracking-wider">{{ __('date') }}</th>
                        <th class="px-5 py-3.5 text-xs font-semibold text-ink-muted uppercase tracking-wider">{{ __('actions') }}</th>
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
                                <form method="POST" action="{{ route('admin.projects.files.destroy', [$project, $file]) }}" onsubmit="return confirm('{{ __("confirm_delete_file") }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-danger hover:text-red-600 text-sm font-medium transition">{{ __('delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <i data-lucide="paperclip" class="w-8 h-8 text-ink-muted/40"></i>
                                    <p class="text-ink-muted text-sm">{{ __('no_files') }}</p>
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
        <h3 class="text-lg font-semibold text-ink mb-5">{{ __('messages') }}</h3>

        <form method="POST" action="{{ route('admin.messages.store', $project) }}" class="mb-6">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $project->user_id }}">
            <div class="flex gap-3">
                <input type="text" name="body" required placeholder="{{ __('type_message') }}"
                    class="input flex-1">
                <button type="submit" class="btn-primary px-6 py-3">
                    {{ __('send') }}
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
                    <p class="text-ink-muted text-sm">{{ __('no_messages') }}</p>
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

    function changeProjectStatus(projectId, status, btn) {
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>';
        }
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
        })
        .catch(() => { if (btn) { btn.disabled = false; } });
    }

    function changeTaskStatus(projectId, taskId, status, btn) {
        if (btn) {
            btn.disabled = true;
        }
        const token = document.querySelector('meta[name="csrf-token"]').content;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/projects/' + projectId + '/tasks/' + taskId + '/status';
        form.innerHTML = '<input type="hidden" name="_token" value="' + token + '"><input type="hidden" name="status" value="' + status + '">';
        document.body.appendChild(form);
        form.submit();
    }
</script>
@endpush
@endsection