<div>
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold text-ink-900 dark:text-ink-50">Mesas</h1>
            <p class="text-sm text-ink-500 dark:text-ink-400">Comandas abertas</p>
        </div>
        <a href="{{ route('tenant.pdv') }}"
           class="px-4 py-2 bg-ink-900 dark:bg-ink-100 text-white dark:text-ink-900 rounded-lg text-sm font-medium hover:bg-ink-800 transition-colors">
            + Nova Venda
        </a>
    </div>

    @if($this->mesas->isEmpty())
        <div class="text-center py-20">
            <svg class="size-12 mx-auto mb-3 text-ink-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M3 14h18M10 3v18M14 3v18"/>
            </svg>
            <p class="text-ink-500 font-medium">Nenhuma mesa aberta</p>
            <p class="text-ink-400 text-sm mt-1">As mesas abertas aparecerão aqui</p>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            @foreach($this->mesas as $comanda)
                <button
                    wire:click="irParaMesa('{{ $comanda->mesa }}')"
                    wire:key="mesa-{{ $comanda->id }}"
                    class="bg-white dark:bg-ink-900 border border-gray-200 dark:border-ink-700 rounded-xl p-4 text-left hover:border-ink-400 hover:shadow-md transition-all"
                >
                    {{-- Número da mesa --}}
                    <div class="flex justify-between items-start mb-3">
                        <div class="size-10 bg-ink-900 dark:bg-ink-100 text-white dark:text-ink-900 rounded-lg flex items-center justify-center font-bold text-lg">
                            {{ $comanda->mesa }}
                        </div>
                        @if($comanda->total_pago > 0)
                            <span class="text-xs px-2 py-0.5 bg-amber-100 text-amber-700 rounded-full font-medium">
                                Parcial
                            </span>
                        @else
                            <span class="text-xs px-2 py-0.5 bg-green-100 text-green-700 rounded-full font-medium">
                                Aberta
                            </span>
                        @endif
                    </div>

                    {{-- Itens --}}
                    <div class="mb-3">
                        <p class="text-xs text-ink-500 mb-1">{{ $comanda->itens->count() }} item(s)</p>
                        <p class="text-xs text-ink-600 dark:text-ink-300 line-clamp-2">
                            {{ $comanda->itens->pluck('produto_nome')->join(', ') }}
                        </p>
                    </div>

                    {{-- Totais --}}
                    <div class="border-t border-gray-100 dark:border-ink-700 pt-2 space-y-1">
                        <div class="flex justify-between text-xs">
                            <span class="text-ink-500">Total</span>
                            <span class="font-semibold text-ink-900 dark:text-ink-50">
                                R$ {{ number_format($comanda->total, 2, ',', '.') }}
                            </span>
                        </div>
                        @if($comanda->total_pago > 0)
                            <div class="flex justify-between text-xs">
                                <span class="text-ink-500">Pago</span>
                                <span class="font-medium text-green-600">
                                    R$ {{ number_format($comanda->total_pago, 2, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-ink-500">Restante</span>
                                <span class="font-medium text-red-600">
                                    R$ {{ number_format($comanda->total_restante, 2, ',', '.') }}
                                </span>
                            </div>
                        @endif
                        <div class="flex justify-between text-xs text-ink-400 mt-1">
                            <span>Aberta às</span>
                            <span>{{ $comanda->created_at->format('H:i') }}</span>
                        </div>
                    </div>
                </button>
            @endforeach
        </div>
    @endif
</div>