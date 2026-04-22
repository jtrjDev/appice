@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
    'icon' => null,
])

@php
    $base = 'inline-flex items-center justify-center gap-2 font-medium rounded-lg transition-all duration-150 select-none disabled:opacity-50 disabled:pointer-events-none';

    $sizes = [
        'sm' => 'h-8 px-3 text-sm',
        'md' => 'h-9 px-4 text-sm',
        'lg' => 'h-10 px-5 text-[15px]',
    ];

    $variants = [
        'primary' => 'bg-ink-900 text-white hover:bg-ink-800 active:bg-ink-950 dark:bg-white dark:text-ink-900 dark:hover:bg-ink-100',
        'secondary' => 'bg-white text-ink-900 border border-ink-200 hover:bg-ink-50 dark:bg-ink-900 dark:text-ink-100 dark:border-ink-800 dark:hover:bg-ink-800',
        'ghost' => 'text-ink-700 hover:bg-ink-100 dark:text-ink-300 dark:hover:bg-ink-800',
        'danger' => 'bg-danger-600 text-white hover:bg-danger-500',
    ];

    $classes = $base . ' ' . ($sizes[$size] ?? $sizes['md']) . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)<x-ui.icon :name="$icon" class="size-4" />@endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)<x-ui.icon :name="$icon" class="size-4" />@endif
        {{ $slot }}
    </button>
@endif