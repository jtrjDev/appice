<div>
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold text-ink-900 dark:text-ink-50">
                Venda #{{ $pedido->numero_pedido }}
            </h1>
            <p class="text-sm text-ink-500 dark:text-ink-400">
                {{ $pedido->created_at->format('d/m/Y H:i') }}
            </p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('tenant.vendas') }}"
               class="px-4 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-sm font-medium hover:bg-ink-50 dark:hover:bg-ink-800">
                Voltar
            </a>

           
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-ink-900 border border-gray-200 dark:border-ink-700 rounded-xl p-4">
            <p class="text-xs text-ink-500 mb-1">Cliente</p>
            <p class="font-semibold text-ink-900 dark:text-ink-50">
                {{ $pedido->cliente->nome ?? 'Consumidor' }}
            </p>
        </div>

        <div class="bg-white dark:bg-ink-900 border border-gray-200 dark:border-ink-700 rounded-xl p-4">
            <p class="text-xs text-ink-500 mb-1">Origem</p>
            <p class="font-semibold text-ink-900 dark:text-ink-50 capitalize">
                {{ $pedido->tipo }}
            </p>
            <p class="text-xs text-ink-400 mt-1">
                {{ $pedido->mesa ? 'Mesa ' . $pedido->mesa : 'Sem mesa' }}
            </p>
        </div>

        <div class="bg-white dark:bg-ink-900 border border-gray-200 dark:border-ink-700 rounded-xl p-4">
            <p class="text-xs text-ink-500 mb-1">Caixa</p>
            <p class="font-semibold text-ink-900 dark:text-ink-50">
                #{{ $pedido->caixa_id }}
            </p>
        </div>
    </div>

    <div class="bg-white dark:bg-ink-900 border border-gray-200 dark:border-ink-700 rounded-xl mb-6">
        <div class="p-4 border-b border-gray-200 dark:border-ink-700">
            <h3 class="font-semibold text-ink-900 dark:text-ink-50">Itens</h3>
        </div>

        <div class="divide-y divide-gray-100 dark:divide-ink-700">
            @foreach($pedido->itens as $item)
                <div class="px-4 py-3 flex justify-between items-center">
                    <div>
                        <p class="font-medium text-ink-900 dark:text-ink-50">{{ $item->produto_nome }}</p>
                        <p class="text-xs text-ink-400">
                            {{ number_format($item->quantidade, $item->quantidade == intval($item->quantidade) ? 0 : 3, ',', '.') }}
                            x R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}
                        </p>
                    </div>

                    <div class="font-semibold text-ink-900 dark:text-ink-50">
                        R$ {{ number_format($item->subtotal, 2, ',', '.') }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white dark:bg-ink-900 border border-gray-200 dark:border-ink-700 rounded-xl p-4">
        <h3 class="font-semibold text-ink-900 dark:text-ink-50 mb-3">Pagamentos</h3>

        <div class="space-y-2 mb-4">
            @foreach(($pedido->pagamentos ?? []) as $pag)
                <div class="flex justify-between text-sm">
                    <span class="text-ink-600 dark:text-ink-300 capitalize">
                        {{ str_replace('_', ' ', $pag['forma']) }}
                    </span>
                    <span class="font-medium text-ink-900 dark:text-ink-50">
                        R$ {{ number_format($pag['valor'], 2, ',', '.') }}
                    </span>
                </div>
            @endforeach
        </div>

        <div class="border-t border-gray-200 dark:border-ink-700 pt-3 flex justify-between">
            <span class="font-semibold text-ink-900 dark:text-ink-50">Total</span>
            <span class="font-bold text-lg text-ink-900 dark:text-ink-50">
                R$ {{ number_format($pedido->total, 2, ',', '.') }}
            </span>
        </div>
    </div>
</div>