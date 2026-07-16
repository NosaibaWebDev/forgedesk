@extends('layouts.app')

@section('title', 'הודעות')
@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm">
    <a href="{{ route('client.dashboard') }}" class="text-ink-muted hover:text-accent transition">בית</a>
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
            <a href="{{ route('client.messages.show', $project) }}" class="flex items-center justify-between px-6 py-5 hover:bg-gray-50 transition">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-accent/10 flex items-center justify-center text-accent">
                        <i data-lucide="message-square" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <p class="font-medium text-ink">{{ $project->title }}</p>
                        @if($project->messages->first())
                            <p class="text-sm text-ink-muted mt-1">{{ $project->messages->first()->body }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    @if($project->unread_count > 0)
                        <span class="rounded-badge bg-accent text-white text-xs px-2.5 py-1 font-medium">
                            {{ $project->unread_count }}
                        </span>
                    @endif
                    @if($project->messages->first())
                        <span class="text-xs text-ink-muted">{{ $project->messages->first()->created_at->diffForHumans() }}</span>
                    @endif
                    <i data-lucide="chevron-left" class="w-5 h-5 text-ink-muted"></i>
                </div>
            </a>
        @empty
            <div class="px-6 py-16 text-center text-ink-muted">
                <i data-lucide="message-square" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
                <p>אין שיחות עדיין.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
