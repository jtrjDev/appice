<div class="px-3 py-2 bg-blue-50 dark:bg-blue-900/20 border-b border-blue-200">
    <div class="flex items-center gap-3">
        <span class="text-sm font-medium">⚖️ Peso (kg):</span>
        <input type="number" wire:model="peso" step="0.1" min="0" 
            class="w-32 px-3 py-1 border rounded-lg text-center"
            placeholder="0.000">
        <span class="text-xs text-ink-500">Ex: 0.500 = 500g</span>
    </div>
</div>