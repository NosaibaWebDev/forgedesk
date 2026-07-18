@extends('layouts.app')

@section('title', __('conversation_with_admin') . ' - ' . $client->name)
@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm min-w-0">
    <a href="{{ route('admin.dashboard') }}" class="text-ink-muted hover:text-accent transition hidden sm:inline">{{ __('home') }}</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted hidden sm:block"></i>
    <a href="{{ route('admin.messages.index') }}" class="text-ink-muted hover:text-accent transition">{{ __('messages') }}</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <span class="text-ink font-medium truncate">{{ $client->name }}</span>
</nav>
@endsection

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-card border border-border shadow-card overflow-hidden">
        <div class="px-4 py-3 sm:px-6 sm:py-4 border-b border-border bg-surface">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('admin.clients.show', $client) }}" class="hover:opacity-80 transition flex-shrink-0">
                    <x-user-avatar :user="$client" />
                </a>
                <div class="min-w-0 flex-1">
                    <a href="{{ route('admin.clients.show', $client) }}" class="font-medium text-ink hover:text-accent transition block truncate">{{ $client->name }}</a>
                    <p class="text-xs text-ink-muted truncate">{{ $project->title }}</p>
                </div>
                <a href="{{ route('admin.messages.index') }}" class="text-xs text-ink-muted hover:text-ink transition flex-shrink-0 whitespace-nowrap">
                    <span class="hidden sm:inline">{{ __('back_to_conversations') }}</span>
                </a>
            </div>
        </div>

        <div class="px-4 py-4 sm:p-6 max-h-[60vh] overflow-y-auto space-y-3 sm:space-y-4 bg-surface" id="messages-container">
            @forelse($messages as $message)
                <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[85%] sm:max-w-md px-4 py-3 sm:px-5 sm:py-3.5 rounded-2xl {{ $message->sender_id === auth()->id() ? 'bg-accent/10 text-ink border border-accent/20' : 'bg-white dark:bg-gray-800 text-ink border border-border shadow-sm' }}">
                        <p class="text-[11px] text-ink-muted mb-1">{{ $message->sender->name }} · {{ $message->created_at->format('d/m/Y H:i') }}</p>
                        <p class="text-sm leading-relaxed break-words">{{ $message->body }}</p>
                    </div>
                </div>
            @empty
                <p class="text-center text-ink-muted py-8">{{ __('no_messages_send_first') }}</p>
            @endforelse
        </div>

        <div class="px-4 py-3 sm:px-4 sm:py-4 border-t border-border">
            <form method="POST" action="{{ route('admin.messages.store', $project) }}">
                @csrf
                <div class="flex gap-2 sm:gap-3">
                    <input type="text" name="body" required placeholder="{{ __('type_message') }}"
                        class="input flex-1 min-w-0">
                    <button type="submit" class="btn-primary flex-shrink-0 whitespace-nowrap">
                        <i data-lucide="send" class="w-4 h-4 sm:hidden"></i>
                        <span class="hidden sm:inline">{{ __('send') }}</span>
                    </button>
                </div>
                @error('body') <p class="text-danger text-sm mt-1">{{ $message }}</p>@enderror
            </form>
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
