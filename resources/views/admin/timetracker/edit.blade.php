@extends('layouts.app')

@section('title', 'עריכת רשומת זמן - מנהל')

@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm">
    <a href="{{ route('admin.dashboard') }}" class="text-ink-muted hover:text-accent transition">בית</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <a href="{{ route('admin.timetracker.index') }}" class="text-ink-muted hover:text-accent transition">מעקב זמן</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <span class="text-ink font-medium">עריכה</span>
</nav>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-card border border-border shadow-card p-6">
        <h3 class="text-lg font-semibold text-ink mb-6">עריכת רשומה</h3>
        <form method="POST" action="{{ route('admin.timetracker.update', $entry) }}">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="label">פרויקט</label>
                    <select name="project_id" id="edit-project" onchange="loadEditTasks(this.value)" class="input">
                        <option value="">ללא פרויקט</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ $entry->project_id == $project->id ? 'selected' : '' }}>{{ $project->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">משימה</label>
                    <select name="task_id" id="edit-task" class="input">
                        <option value="">ללא משימה</option>
                    </select>
                </div>
                <div>
                    <label class="label">תיאור</label>
                    <input type="text" name="description" value="{{ $entry->description }}" placeholder="תיאור העבודה" class="input">
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="label">תאריך</label>
                        <input type="date" name="date" value="{{ $entry->date->format('Y-m-d') }}" required class="input">
                    </div>
                    <div>
                        <label class="label">התחלה</label>
                        <input type="time" name="start_time" value="{{ $entry->start_time->format('H:i') }}" required class="input">
                    </div>
                    <div>
                        <label class="label">סיום</label>
                        <input type="time" name="end_time" value="{{ $entry->end_time ? $entry->end_time->format('H:i') : '' }}" required class="input">
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="btn-primary">
                        שמור שינויים
                    </button>
                    <a href="{{ route('admin.timetracker.index') }}" class="btn-ghost">
                        ביטול
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
                    const selected = t.id == currentVal ? 'selected' : '';
                    taskSelect.innerHTML += '<option value="' + t.id + '" ' + selected + '>' + t.title + '</option>';
                });
            });
    }
    @if($entry->project_id)
    loadEditTasks('{{ $entry->project_id }}');
    @endif
</script>
@endpush
@endsection
