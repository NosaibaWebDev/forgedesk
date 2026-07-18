@props(['value' => 0, 'size' => 'sm'])
@php
    $height = match($size) {
        'xs' => 'h-1.5',
        'sm' => 'h-2',
        'md' => 'h-3',
        default => 'h-2',
    };
@endphp
<div class="flex items-center gap-2.5">
    <div class="flex-1 bg-gray-100 rounded-full {{ $height }}">
        <div class="bg-accent {{ $height }} rounded-full transition-all" style="width: {{ $value }}%"></div>
    </div>
    <span class="text-sm font-medium text-ink-secondary">{{ $value }}%</span>
</div>