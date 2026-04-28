<div class="flex-1 min-h-0 overflow-y-auto p-3 pt-0 scrollbar-thin">
    <div class="grid grid-cols-[repeat(auto-fill,minmax(105px,1fr))] gap-2 content-start">
        @forelse($produtos as $produto)
            <button wire:click="adicionarProduto({{ $produto->id }})" wire:key="prod-{{ $produto->id }}"
                class="group bg-white dark:bg-ink-900 rounded-lg border border-gray-200 dark:border-ink-700 p-2 text-left hover:shadow-lg transition-all active:scale-95">
                <div class="flex flex-col items-center gap-1">
                    <div class="w-full h-12 bg-gradient-to-br from-ink-50 to-gray-100 rounded-md flex items-center justify-center">
                        <span class="text-2xl">{{ $produto->icone ?? '📦' }}</span>
                    </div>
                    <div class="text-center w-full">
                        <p class="font-semibold text-[11px] leading-tight line-clamp-2">{{ $produto->nome }}</p>
                        <p class="text-xs font-bold text-primary-600 mt-1">
                            R$ {{ number_format($produto->preco_atual, 2, ',', '.') }}
                        </p>
                    </div>
                </div>
            </button>
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-ink-400">Nenhum produto encontrado</p>
            </div>
        @endforelse
    </div>
</div>