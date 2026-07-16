@extends('layouts.app')

@section('title', 'עריכת פרויקט - מנהל')
@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm">
    <a href="{{ route('admin.dashboard') }}" class="text-ink-muted hover:text-accent transition">בית</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <a href="{{ route('admin.projects.index') }}" class="text-ink-muted hover:text-accent transition">פרויקטים</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <a href="{{ route('admin.projects.show', $project) }}" class="text-ink-muted hover:text-accent transition">{{ $project->title }}</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <span class="text-ink font-medium">עריכה</span>
</nav>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-card border border-border shadow-card p-6">
        <form method="POST" action="{{ route('admin.projects.update', $project) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="title" class="label">שם הפרויקט *</label>
                <input type="text" id="title" name="title" value="{{ old('title', $project->title) }}" required
                    class="input">
                @error('title') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="user_id" class="label">לקוח *</label>
                <select id="user_id" name="user_id" required
                    class="input">
                    <option value="">בחר לקוח</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ old('user_id', $project->user_id) == $client->id ? 'selected' : '' }}>
                            {{ $client->name }} - {{ $client->email }}
                        </option>
                    @endforeach
                </select>
                @error('user_id') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="label">תיאור</label>
                <textarea id="description" name="description" rows="4"
                    class="input">{{ old('description', $project->description) }}</textarea>
                @error('description') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="status" class="label">סטטוס *</label>
                    <select id="status" name="status" required
                        class="input">
                        <option value="pending" {{ old('status', $project->status) === 'pending' ? 'selected' : '' }}>ממתין</option>
                        <option value="in_progress" {{ old('status', $project->status) === 'in_progress' ? 'selected' : '' }}>בתהליך</option>
                        <option value="review" {{ old('status', $project->status) === 'review' ? 'selected' : '' }}>בבדיקה</option>
                        <option value="completed" {{ old('status', $project->status) === 'completed' ? 'selected' : '' }}>הושלם</option>
                        <option value="cancelled" {{ old('status', $project->status) === 'cancelled' ? 'selected' : '' }}>בוטל</option>
                    </select>
                    @error('status') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="priority" class="label">עדיפות *</label>
                    <select id="priority" name="priority" required
                        class="input">
                        <option value="low" {{ old('priority', $project->priority) === 'low' ? 'selected' : '' }}>נמוכה</option>
                        <option value="medium" {{ old('priority', $project->priority) === 'medium' ? 'selected' : '' }}>בינונית</option>
                        <option value="high" {{ old('priority', $project->priority) === 'high' ? 'selected' : '' }}>גבוהה</option>
                        <option value="urgent" {{ old('priority', $project->priority) === 'urgent' ? 'selected' : '' }}>דחופה</option>
                    </select>
                    @error('priority') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="budget" class="label">תקציב (₪)</label>
                    <input type="number" id="budget" name="budget" value="{{ old('budget', $project->budget) }}" min="0" step="0.01"
                        class="input">
                    @error('budget') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="paid_amount" class="label">סכום שולם (₪)</label>
                    <input type="number" id="paid_amount" name="paid_amount" value="{{ old('paid_amount', $project->paid_amount) }}" min="0" step="0.01"
                        class="input">
                    @error('paid_amount') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="start_date" class="label">תאריך התחלה</label>
                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}"
                        class="input">
                    @error('start_date') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="due_date" class="label">תאריך יעד</label>
                    <input type="date" id="due_date" name="due_date" value="{{ old('due_date', $project->due_date?->format('Y-m-d')) }}"
                        class="input">
                    @error('due_date') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="notes" class="label">הערות</label>
                <textarea id="notes" name="notes" rows="3"
                    class="input">{{ old('notes', $project->notes) }}</textarea>
                @error('notes') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="btn-primary">
                    עדכן פרויקט
                </button>
                <a href="{{ route('admin.projects.show', $project) }}" class="btn-ghost">
                    ביטול
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
