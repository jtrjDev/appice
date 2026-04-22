@props(['padding' => true])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-ink-900 border border-ink-200 dark:border-ink-800 rounded-xl ' . ($padding ? 'p-6' : '')]) }}>
    {{ $slot }}
</div>