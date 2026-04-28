<div class="p-3 space-y-2 shrink-0">
    {{-- Barra de busca --}}
    <div class="grid grid-cols-12 gap-2">
        <div class="col-span-12 sm:col-span-6 relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-ink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
            </svg>
            <input type="text" wire:model.live.debounce.300ms="busca" placeholder="Buscar produto..." 
                class="w-full pl-9 pr-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-ink-500">
        </div>
        <div class="col-span-8 sm:col-span-4 relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-ink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h1m1 0h1M4 12h1m1 0h1M4 18h1m1 0h1M15 6h1m1 0h1M15 12h1m1 0h1M15 18h1m1 0h1M9 3v18M12 3v18"/>
            </svg>
            <input type="text" wire:model="codigoProduto" wire:keydown.enter="buscarPorCodigo" 
                placeholder="Código / ID" id="campo-codigo" 
                class="w-full pl-9 pr-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-ink-500">
        </div>
        <div class="col-span-4 sm:col-span-2 relative">
            <input type="text" placeholder="1,000" id="campo-quantidade" wire:model="quantidadeInput" step="0.001"
                x-data="{ raw: '' }"
                x-on:keydown.enter="
                    let num = raw === '' ? 1 : parseInt(raw) / 1000;
                    $wire.set('quantidadeInput', num).then(() => {
                        document.getElementById('campo-codigo').dispatchEvent(new KeyboardEvent('keydown', { key: 'Enter', bubbles: true }));
                    });
                "
                x-on:keydown="
                    if (event.key === 'Enter') return;
                    if (event.key === 'Backspace') { raw = raw.slice(0, -1); }
                    else if (event.key >= '0' && event.key <= '9') { raw = raw + event.key; }
                    else { return; }
                    event.preventDefault();
                    let num = raw === '' ? 0 : parseInt(raw) / 1000;
                    $el.value = num.toLocaleString('pt-BR', { minimumFractionDigits: 3, maximumFractionDigits: 3 });
                "
                x-on:focus="raw = ''; $el.value = '1,000'"
                x-on:blur="
                    let num = raw === '' ? 1 : parseInt(raw) / 1000;
                    $wire.set('quantidadeInput', num);
                    raw = '';
                "
                class="w-full pl-3 pr-2 py-2 border rounded-lg text-sm text-center focus:ring-2 focus:ring-ink-500">
        </div>
    </div>

    {{-- Categorias --}}
    <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-thin">
        <button wire:click="selecionarCategoria(null)" 
            class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all
                {{ !$categoriaSelecionada ? 'bg-ink-900 text-white shadow-md' : 'bg-gray-100 text-ink-700 hover:bg-gray-200' }}">
            📦 TODOS
        </button>
        @foreach($categorias as $categoria)
            <button wire:click="selecionarCategoria({{ $categoria->id }})" 
                class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all
                    {{ $categoriaSelecionada == $categoria->id ? 'bg-ink-900 text-white shadow-md' : 'bg-gray-100 text-ink-700 hover:bg-gray-200' }}">
                {{ $categoria->icone ?? '📌' }} {{ $categoria->nome }}
            </button>
        @endforeach
    </div>
</div>