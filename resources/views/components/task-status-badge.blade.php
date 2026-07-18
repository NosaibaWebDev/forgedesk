@props(['status'])
@php
    $statusEnum = is_string($status) ? \App\Enums\TaskStatus::tryFrom($status) : $status;
    $label = $statusEnum?->label() ?? $status;
    $classes = $statusEnum?->badgeClasses() ?? 'bg-gray-50 text-gray-700 ring-gray-600/20';
@endphp
<span class="rounded-badge px-3 py-1 text-xs font-medium ring-1 ring-inset {{ $classes }}">
    {{ $label }}
</span>