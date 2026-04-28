<div class="px-3 py-2 bg-amber-50 dark:bg-amber-900/20 border-b border-amber-200">
    <div class="flex items-center gap-4 text-sm">
        <label class="flex items-center gap-2">
            <input type="checkbox" wire:model="meiaPorcao" class="rounded">
            <span>🍕 Meia Pizza</span>
        </label>
        <select wire:model="tamanhoSelecionado" class="px-2 py-1 text-xs border rounded">
            <option value="">Tamanho</option>
            @foreach($tamanhosDisponiveis as $tamanho)
                <option value="{{ $tamanho }}">{{ $tamanho }}</option>
            @endforeach
        </select>
    </div>
</div>