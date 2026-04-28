<div class="bg-white dark:bg-ink-900 rounded-xl border shadow-sm overflow-hidden flex flex-col flex-1">
    <div class="px-3 py-2 bg-gray-50 border-b flex justify-between items-center">
        <span class="font-bold text-sm">🛒 Carrinho ({{ number_format($this->totalItens, 0) }})</span>
        @if(count($carrinho) > 0)
            <button wire:click="limparCarrinho" class="text-xs text-red-500 hover:text-red-700">Limpar</button>
        @endif
    </div>

    <div class="flex-1 min-h-0 overflow-y-auto divide-y">
        @forelse($carrinho as $chave => $item)
            <div class="px-3 py-2 hover:bg-gray-50" wire:key="item-{{ $chave }}">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <p class="font-medium text-sm truncate">{{ $item['nome'] }}</p>
                        @if(isset($item['observacao']))
                            <p class="text-[10px] text-ink-400">{{ $item['observacao'] }}</p>
                        @endif
                        <p class="text-xs text-ink-500">{{ $item['preco_formatado'] }}</p>
                    </div>
                    <button wire:click="removerProduto({{ $item['id'] }})" class="text-red-400">✕</button>
                </div>
                <div class="flex justify-between items-center mt-2">
                    <div class="flex items-center gap-1">
                        <button wire:click="atualizarQuantidade({{ $item['id'] }}, {{ $item['quantidade'] - 1 }})" 
                            class="size-6 rounded-md bg-gray-100 flex items-center justify-center">−</button>
                        <span class="w-12 text-center text-sm">{{ number_format($item['quantidade'], $item['quantidade'] == intval($item['quantidade']) ? 0 : 2, ',', '.') }}</span>
                        <button wire:click="atualizarQuantidade({{ $item['id'] }}, {{ $item['quantidade'] + 1 }})" 
                            class="size-6 rounded-md bg-gray-100 flex items-center justify-center">+</button>
                    </div>
                    <p class="font-bold text-sm">R$ {{ number_format($item['subtotal'], 2, ',', '.') }}</p>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-12">
                <svg class="size-12 text-ink-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <p class="text-sm text-ink-400">Carrinho vazio</p>
            </div>
        @endforelse
    </div>

    @php
        $totalPago = round(collect($pagamentos)->sum('valor'), 2);
        $pendente = max(0, $this->totalCarrinho - $totalPago);
    @endphp

    <div class="p-3 border-t bg-gray-50">
        <div class="flex justify-between mb-2">
            <span class="text-sm">Total:</span>
            <span class="text-xl font-bold text-primary-600">R$ {{ number_format($this->totalCarrinho, 2, ',', '.') }}</span>
        </div>
        <div class="flex justify-between text-xs mb-2">
            <span>Pago:</span>
            <span class="text-green-600">R$ {{ number_format($totalPago, 2, ',', '.') }}</span>
        </div>
        <div class="flex justify-between text-xs mb-3">
            <span>Restante:</span>
            <span class="{{ $pendente > 0 ? 'text-red-600' : 'text-green-600' }} font-bold">
                R$ {{ number_format($pendente, 2, ',', '.') }}
            </span>
        </div>
        @if($modoComanda && $mesa)
            <button wire:click="salvarComanda" class="w-full mb-2 py-2 border-2 border-amber-500 text-amber-600 rounded-lg font-bold text-sm">
                💾 Salvar Mesa ({{ $mesa }})
            </button>
        @endif
        <button @click="showPayment = true" @if(empty($carrinho)) disabled @endif
            class="w-full py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg font-bold text-sm shadow-lg disabled:opacity-40">
            💳 Finalizar Venda
        </button>
    </div>
</div>