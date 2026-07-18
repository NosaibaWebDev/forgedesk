@props(['messages' => collect(), 'currentUserId' => null])
<div class="space-y-4 max-h-96 overflow-y-auto">
    @forelse($messages->sortBy('created_at') as $message)
        <div class="flex {{ $message->sender_id === $currentUserId ? 'justify-end' : 'justify-start' }}">
            <div class="max-w-md px-5 py-3.5 rounded-2xl {{ $message->sender_id === $currentUserId ? 'bg-accent/10 text-ink border border-accent/20' : 'bg-white text-ink border border-border shadow-sm' }}">
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
