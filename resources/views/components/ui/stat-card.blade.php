@props([
    'label' => '',
    'value' => '',
    'icon' => null,
    'trend' => null,
    'trendLabel' => null,
])

<div class="bg-white dark:bg-ink-900 border border-ink-200 dark:border-ink-800 rounded-xl p-5 transition-all hover:border-ink-300 dark:hover:border-ink-700">
    <div class="flex items-start justify-between">
        <div class="space-y-1">
            <p class="text-xs font-medium text-ink-500 dark:text-ink-400 uppercase tracking-wider">{{ $label }}</p>
            <p class="text-3xl font-semibold text-ink-900 dark:text-ink-50 tabular-nums">{{ $value }}</p>
        </div>
        @if($icon)
            <div class="size-9 rounded-lg bg-ink-100 dark:bg-ink-800 flex items-center justify-center">
                <x-ui.icon :name="$icon" class="size-4 text-ink-700 dark:text-ink-300" />
            </div>
        @endif
    </div>
    @if($trend !== null)
        <div class="mt-3 flex items-center gap-1 text-xs">
            <span class="font-medium {{ $trend >= 0 ? 'text-success-600' : 'text-danger-600' }}">
                {{ $trend >= 0 ? '+' : '' }}{{ $trend }}%
            </span>
            @if($trendLabel)
                <span class="text-ink-500">{{ $trendLabel }}</span>
            @endif
        </div>
    @endif
</div>