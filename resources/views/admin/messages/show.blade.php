@extends('layouts.app')

@section('title', 'שיחה - ' . $client->name . ' - מנהל')
@section('breadcrumbs')
<nav class="flex items-center gap-2 text-sm">
    <a href="{{ route('admin.dashboard') }}" class="text-ink-muted hover:text-accent transition">בית</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <a href="{{ route('admin.messages.index') }}" class="text-ink-muted hover:text-accent transition">הודעות</a>
    <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
    <span class="text-ink font-medium">{{ $client->name }}</span>
</nav>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-card border border-border shadow-card">
        <div class="p-4 border-b border-border bg-surface rounded-t-card">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.clients.show', $client) }}" class="hover:opacity-80 transition">
                    <x-user-avatar :user="$client" />
                </a>
                <div>
                    <a href="{{ route('admin.clients.show', $client) }}" class="font-medium text-ink hover:text-accent transition">{{ $client->name }}</a>
                    <p class="text-xs text-ink-muted">{{ $project->title }}</p>
                </div>
                <div class="mr-auto flex items-center gap-2">
                    <a href="{{ route('admin.messages.index') }}" class="text-xs text-ink-muted hover:text-ink transition">← חזרה לשיחות</a>
                </div>
            </div>
        </div>

        <div class="p-6 max-h-[500px] overflow-y-auto space-y-4 bg-surface" id="messages-container">
            @forelse($messages as $message)
                <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-start' : 'justify-end' }}">
                    <div class="max-w-md px-5 py-3.5 rounded-2xl {{ $message->sender_id === auth()->id() ? 'bg-accent/10 text-ink border border-accent/20' : 'bg-white text-ink border border-border shadow-sm' }}">
                        <p class="text-xs text-ink-muted mb-1">{{ $message->sender->name }} · {{ $message->created_at->format('d/m/Y H:i') }}</p>
                        <p class="text-sm leading-relaxed">{{ $message->body }}</p>
                    </div>
                </div>
            @empty
                <p class="text-center text-ink-muted py-8">אין הודעות עדיין. שלח הודעה ראשונה!</p>
            @endforelse
        </div>

        <div class="p-4 border-t border-border">
            <form method="POST" action="{{ route('admin.messages.store', $project) }}">
                @csrf
                <div class="flex gap-3">
                    <input type="text" name="body" required placeholder="הקלד הודעה..."
                        class="input flex-1">
                    <button type="submit" class="btn-primary">
                        שלח
                    </button>
                </div>
                @error('body') <p class="text-danger text-sm mt-1">{{ $message }}</p> @enderror
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
