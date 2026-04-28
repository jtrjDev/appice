<div class="px-3 py-2 bg-purple-50 dark:bg-purple-900/20 border-b border-purple-200">
    <div class="flex items-center gap-4 text-sm">
        <label class="flex items-center gap-2">
            <input type="checkbox" wire:model="meiaPorcao" class="rounded">
            <span>🥣 Meia porção</span>
        </label>
        <select wire:model="tamanhoSelecionado" class="px-2 py-1 text-xs border rounded">
            <option value="">Tamanho</option>
            <option value="Pequeno">Pequeno (300ml)</option>
            <option value="Médio">Médio (500ml)</option>
            <option value="Grande">Grande (700ml)</option>
        </select>
    </div>
</div>