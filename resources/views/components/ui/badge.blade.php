@props(['variant' => 'neutral'])

@php
    $variants = [
        'neutral' => 'bg-ink-100 text-ink-700 dark:bg-ink-800 dark:text-ink-300',
        'success' => 'bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-500',
        'warning' => 'bg-warning-50 text-warning-600 dark:bg-warning-500/10 dark:text-warning-500',
        'danger'  => 'bg-danger-50 text-danger-600 dark:bg-danger-500/10 dark:text-danger-500',
        'info'    => 'bg-info-50 text-info-600 dark:bg-info-500/10 dark:text-info-500',
    ];
    $classes = 'inline-flex items-center gap-1 h-5 px-2 text-xs font-medium rounded-md ' . ($variants[$variant] ?? $variants['neutral']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>