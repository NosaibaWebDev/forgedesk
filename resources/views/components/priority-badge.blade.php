@props(['priority'])
@php
    $priorityEnum = is_string($priority) ? \App\Enums\Priority::tryFrom($priority) : $priority;
    $label = $priorityEnum?->label() ?? $priority;
    $textClass = $priorityEnum?->textColor() ?? 'text-ink-muted';
@endphp
<span class="font-medium {{ $textClass }}">{{ $label }}</span>