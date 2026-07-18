@extends('layouts.app')

@section('title', __('edit_entry_title'))

@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm">
    <a href="{{ route('admin.dashboard') }}" class="text-ink-muted hover:text-accent transition">{{ __('home') }}</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <a href="{{ route('admin.timetracker.index') }}" class="text-ink-muted hover:text-accent transition">{{ __('time_tracking') }}</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <span class="text-ink font-medium">{{ __('edit') }}</span>
</nav>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-card border border-border shadow-card p-6">
        <h3 class="text-lg font-semibold text-ink mb-6">{{ __('edit_entry') }}</h3>
        <form method="POST" action="{{ route('admin.timetracker.update', $entry) }}">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="label">{{ __('project_name') }}</label>
                    <select name="project_id" id="edit-project" onchange="loadEditTasks(this.value)" class="input">
                        <option value="">{{ __('no_project') }}</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ $entry->project_id == $project->id ? 'selected' : '' }}>{{ $project->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">{{ __('task_label') }}</label>
                    <select name="task_id" id="edit-task" class="input">
                        <option value="">{{ __('no_task') }}</option>
                    </select>
                </div>
                <div>
                    <label class="label">{{ __('description') }}</label>
                    <input type="text" name="description" value="{{ $entry->description }}" placeholder="{{ __('description_placeholder') }}" class="input">
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="label">{{ __('date') }}</label>
                        <input type="date" name="date" value="{{ $entry->date->format('Y-m-d') }}" required class="input">
                    </div>
                    <div>
                        <label class="label">{{ __('start_time') }}</label>
                        <input type="time" name="start_time" value="{{ $entry->start_time->format('H:i') }}" required class="input">
                    </div>
                    <div>
                        <label class="label">{{ __('end_time') }}</label>
                        <input type="time" name="end_time" value="{{ $entry->end_time ? $entry->end_time->format('H:i') : '' }}" required class="input">
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="btn-primary">
                        {{ __('save') }}
                    </button>
                    <a href="{{ route('admin.timetracker.index') }}" class="btn-ghost">
                        {{ __('cancel') }}
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function loadEditTasks(projectId) {
        const taskSelect = document.getElementById('edit-task');
        const currentVal = '{{ $entry->task_id }}';
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
                    if (t.id == currentVal) opt.selected = true;
                    taskSelect.appendChild(opt);
                });
            });
    }
    @if($entry->project_id)
    loadEditTasks('{{ $entry->project_id }}');
    @endif
</script>
@endpush
@endsection