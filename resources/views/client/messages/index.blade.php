@extends('layouts.app')

@section('title', __('messages'))
@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm">
    <a href="{{ route('client.dashboard') }}" class="text-ink-muted hover:text-accent transition">{{ __('home') }}</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <span class="text-ink font-medium">{{ __('messages') }}</span>
</nav>
@endsection

@section('content')
<div class="bg-white rounded-card border border-border shadow-card overflow-hidden">
    <div class="px-6 py-5 border-b border-border flex items-center justify-between">
        <h2 class="text-lg font-semibold text-ink">{{ __('messages_list') }}</h2>
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="btn-primary inline-flex items-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i>
                {{ __('start_conversation') }}
            </button>
            <div x-show="open" x-transition x-cloak @click.away="open = false"
                 class="absolute top-full mt-2 left-0 w-72 rounded-card py-1 z-50 shadow-elevated"
                 style="background:var(--color-card); border:1px solid var(--color-border);">
                @forelse($projects as $project)
                    <a href="{{ route('client.messages.show', $project) }}" class="flex items-center gap-3 px-4 py-3 text-sm hover:bg-gray-50 transition" style="color:var(--color-ink);">
                        <i data-lucide="folder" class="w-4 h-4 text-ink-muted flex-shrink-0"></i>
                        <span class="truncate">{{ $project->title }}</span>
                    </a>
                @empty
                    <p class="px-4 py-3 text-sm text-ink-muted">{{ __('no_projects') }}</p>
                @endforelse
            </div>
        </div>
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
                <p>{{ __('no_conversations') }}</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
