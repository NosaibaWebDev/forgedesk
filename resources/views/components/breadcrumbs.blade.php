@props(['items' => []])
<nav class="flex items-center gap-2 text-sm" aria-label="{{ __('breadcrumb_nav') }}">
    @foreach($items as $index => $item)
        @if($index > 0)
            <i data-lucide="chevron-left" class="w-3.5 h-3.5 text-ink-muted"></i>
        @endif
        @if(isset($item['url']))
            <a href="{{ $item['url'] }}" class="text-ink-muted hover:text-accent transition">{{ $item['label'] }}</a>
        @else
            <span class="text-ink font-medium">{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>
