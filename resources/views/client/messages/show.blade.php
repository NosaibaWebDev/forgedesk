@extends('layouts.app')

@section('title', __('messages') . ' - ' . $project->title)
@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm">
    <a href="{{ route('client.dashboard') }}" class="text-ink-muted hover:text-accent transition">{{ __('home') }}</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <a href="{{ route('client.messages.index') }}" class="text-ink-muted hover:text-accent transition">{{ __('messages') }}</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <span class="text-ink font-medium">{{ $project->title }}</span>
</nav>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-card border border-border shadow-card overflow-hidden">
        <div class="px-6 py-4 border-b border-border bg-gray-50/80">
            <p class="font-medium text-ink">{{ $project->title }}</p>
            <p class="text-xs text-ink-muted mt-0.5">{{ __('conversation_with_admin') }}</p>
        </div>

        <div class="p-6 max-h-[500px] overflow-y-auto space-y-4" id="messages-container">
            @forelse($messages as $message)
                <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                    <div class="{{ $message->sender_id === auth()->id() ? 'max-w-md px-5 py-3.5 rounded-2xl bg-accent/10 text-ink border border-accent/20' : 'max-w-md px-5 py-3.5 rounded-2xl bg-white text-ink border border-border shadow-sm' }}">
                        <p class="text-xs text-ink-muted mb-1">{{ $message->sender->name }} · {{ $message->created_at->format('d/m/Y H:i') }}</p>
                        <p class="text-sm leading-relaxed">{{ $message->body }}</p>
                    </div>
                </div>
            @empty
                <p class="text-center text-ink-muted py-12">{{ __('no_messages_send_first') }}</p>
            @endforelse
        </div>

        <div class="px-6 py-4 border-t border-border bg-gray-50/80">
            <form method="POST" action="{{ route('client.messages.store', $project) }}">
                @csrf
                <div class="flex gap-3">
                    <input type="text" name="body" required placeholder="{{ __('type_message') }}"
                        class="input flex-1">
                    <button type="submit" class="btn-primary">
                        {{ __('send') }}
                    </button>
                </div>
                @error('body') <p class="text-danger text-xs mt-1.5">{{ $message }}</p> @enderror
            </form>
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
