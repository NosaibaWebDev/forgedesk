@props(['icon' => 'inbox', 'title' => __('no_data'), 'description' => '', 'actionUrl' => '', 'actionLabel' => ''])
<div class="flex flex-col items-center justify-center py-12 px-4">
    <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
        <i data-lucide="{{ $icon }}" class="w-8 h-8 text-ink-muted/40"></i>
    </div>
    <h3 class="text-lg font-medium text-ink mb-1">{{ $title }}</h3>
    @if($description)
        <p class="text-sm text-ink-muted text-center max-w-sm mb-4">{{ $description }}</p>
    @endif
    @if($actionUrl)
        <a href="{{ $actionUrl }}" class="btn-primary">
            {{ $actionLabel }}
        </a>
    @endif
    {{ $slot }}
</div>
