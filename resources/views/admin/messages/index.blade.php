@extends('layouts.app')

@section('title', 'הודעות - מנהל')
@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm">
    <a href="{{ route('admin.dashboard') }}" class="text-ink-muted hover:text-accent transition">בית</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <span class="text-ink font-medium">הודעות</span>
</nav>
@endsection

@section('content')
<div class="bg-white rounded-card border border-border shadow-card overflow-hidden">
    <div class="px-6 py-5 border-b border-border">
        <h2 class="text-lg font-semibold text-ink">רשימת שיחות</h2>
    </div>
    <div class="divide-y divide-border">
        @forelse($conversations as $project)
            <a href="{{ route('admin.messages.show', $project) }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 sm:p-6 hover:bg-gray-50 transition cursor-pointer">
                <div class="flex items-center gap-4 min-w-0">
                    <x-user-avatar :user="$project->user" size="lg" />
                    <div class="min-w-0">
                        <p class="font-medium text-ink">{{ $project->user->name }}</p>
                        <p class="text-sm text-ink-secondary truncate">{{ $project->title }}</p>
                        @if($project->messages->first())
                            <p class="text-sm text-ink-muted mt-1 truncate max-w-xs sm:max-w-md">{{ $project->messages->first()->body }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center justify-between sm:justify-end gap-4 shrink-0">
                    @if($project->unread_count > 0)
                        <span class="bg-danger text-white text-xs rounded-badge px-3 py-1 font-medium">
                            {{ $project->unread_count }}
                        </span>
                    @endif
                    @if($project->messages->first())
                        <span class="text-xs text-ink-muted">{{ $project->messages->first()->created_at->diffForHumans() }}</span>
                    @endif
                    <i data-lucide="chevron-right" class="w-5 h-5 text-ink-muted"></i>
                </div>
            </a>
        @empty
            <div class="px-6 py-16 text-center text-ink-muted">
                אין שיחות עדיין.
            </div>
        @endforelse
    </div>
</div>
@endsection
