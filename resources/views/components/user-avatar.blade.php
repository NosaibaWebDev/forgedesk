@props([
    'user' => null,
    'size' => 'md',
    'variant' => 'accent',
    'class' => '',
])

@php
$sizeMap = [
    'xs' => ['class' => 'w-6 h-6 text-[10px]', 'px' => 24],
    'sm' => ['class' => 'w-8 h-8 text-xs', 'px' => 32],
    'md' => ['class' => 'w-10 h-10 text-sm', 'px' => 40],
    'lg' => ['class' => 'w-12 h-12 text-base', 'px' => 48],
    'xl' => ['class' => 'w-16 h-16 text-2xl', 'px' => 64],
];
$variants = [
    'accent' => 'bg-accent/10 text-accent',
    'green' => 'bg-green-50 text-green-600',
    'white' => 'bg-white text-accent border border-border',
];
$sizeData = $sizeMap[$size] ?? $sizeMap['md'];
$sizeClass = $sizeData['class'];
$px = $sizeData['px'];
$variantClass = $variants[$variant] ?? $variants['accent'];
@endphp

@if($user && $user->avatar && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar))
    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($user->avatar) }}"
         alt="{{ $user->name }}"
         width="{{ $px }}" height="{{ $px }}"
         class="{{ $sizeClass }} rounded-full object-cover flex-shrink-0 {{ $class }}"
         style="border:2px solid var(--color-border);">
@else
    <div class="{{ $sizeClass }} rounded-full {{ $variantClass }} flex items-center justify-center flex-shrink-0 {{ $class }}">
        <i data-lucide="user" class="w-1/2 h-1/2"></i>
    </div>
@endif
