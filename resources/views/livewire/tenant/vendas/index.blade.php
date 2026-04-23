<div>
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold text-ink-900 dark:text-ink-50">Histórico de Vendas</h1>
            <p class="text-sm text-ink-500 dark:text-ink-400">Consulte vendas e reimprima cupons</p>
        </div>

        <a href="{{ route('tenant.pdv') }}"
           class="px-4 py-2 bg-ink-900 dark:bg-ink-100 text-white dark:text-ink-900 rounded-lg text-sm font-medium hover:bg-ink-800 transition-colors">
            + Nova Venda
        </a>
    </div>

    {{-- filtros --}}
    <div class="bg-white dark:bg-ink-900 border border-gray-200 dark:border-ink-700 rounded-xl p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-3">
            <div class="xl:col-span-2">
                <label class="block text-xs text-ink-500 mb-1">Buscar</label>
                <input type="text" wire:model.live.debounce.400ms="busca"
                       placeholder="Pedido, mesa ou cliente"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-sm dark:bg-ink-800 dark:text-ink-100">
            </div>

            <div>
                <label class="block text-xs text-ink-500 mb-1">De</label>
                <input type="date" wire:model.live="dataInicio"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-sm dark:bg-ink-800 dark:text-ink-100">
            </div>

            <div>
                <label class="block text-xs text-ink-500 mb-1">Até</label>
                <input type="date" wire:model.live="dataFim"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-sm dark:bg-ink-800 dark:text-ink-100">
            </div>

            <div>
                <label class="block text-xs text-ink-500 mb-1">Forma</label>
                <select wire:model.live="formaPagamento"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-sm dark:bg-ink-800 dark:text-ink-100">
                    <option value="">Todas</option>
                    <option value="dinheiro">Dinheiro</option>
                    <option value="pix">PIX</option>
                    <option value="cartao_credito">Cartão crédito</option>
                    <option value="cartao_debito">Cartão débito</option>
                </select>
            </div>

            <div>
                <label class="block text-xs text-ink-500 mb-1">Tipo</label>
                <select wire:model.live="tipo"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-sm dark:bg-ink-800 dark:text-ink-100">
                    <option value="">Todos</option>
                    <option value="balcao">Balcão</option>
                    <option value="mesa">Mesa</option>
                </select>
            </div>

            <div>
                <label class="block text-xs text-ink-500 mb-1">Mesa</label>
                <input type="text" wire:model.live.debounce.300ms="mesa"
                       placeholder="Nº da mesa"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-sm dark:bg-ink-800 dark:text-ink-100">
            </div>

            <div>
                <label class="block text-xs text-ink-500 mb-1">Caixa</label>
                <select wire:model.live="caixaId"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-sm dark:bg-ink-800 dark:text-ink-100">
                    <option value="">Todos</option>
                    @foreach($this->caixas as $caixa)
                        <option value="{{ $caixa->id }}">
                            Caixa #{{ $caixa->id }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs text-ink-500 mb-1">Status</label>
                <select wire:model.live="status"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-sm dark:bg-ink-800 dark:text-ink-100">
                    <option value="">Todos</option>
                    <option value="entregue">Entregue</option>
                    <option value="cancelado">Cancelado</option>
                </select>
            </div>
        </div>

        <div class="mt-4 flex justify-end">
            <button wire:click="limparFiltros"
                    class="px-4 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-sm font-medium hover:bg-ink-50 dark:hover:bg-ink-800 transition-colors">
                Limpar filtros
            </button>
        </div>
    </div>

    {{-- tabela --}}
    <div class="bg-white dark:bg-ink-900 border border-gray-200 dark:border-ink-700 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-ink-50 dark:bg-ink-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-ink-500 uppercase">Pedido</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-ink-500 uppercase">Data</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-ink-500 uppercase">Origem</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-ink-500 uppercase">Cliente</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-ink-500 uppercase">Pagamentos</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-ink-500 uppercase">Total</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-ink-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-ink-700">
                    @forelse($this->pedidos as $pedido)
                        <tr class="hover:bg-ink-50 dark:hover:bg-ink-800">
                            <td class="px-4 py-3">
                                <div class="font-semibold text-ink-900 dark:text-ink-50">
                                    #{{ $pedido->numero_pedido }}
                                </div>
                                <div class="text-xs text-ink-400">
                                    Caixa #{{ $pedido->caixa_id }}
                                </div>
                            </td>

                            <td class="px-4 py-3 text-ink-600 dark:text-ink-300">
                                <div>{{ $pedido->created_at->format('d/m/Y') }}</div>
                                <div class="text-xs text-ink-400">{{ $pedido->created_at->format('H:i') }}</div>
                            </td>

                            <td class="px-4 py-3 text-ink-600 dark:text-ink-300">
                                <div class="capitalize">{{ $pedido->tipo }}</div>
                                <div class="text-xs text-ink-400">
                                    {{ $pedido->mesa ? 'Mesa ' . $pedido->mesa : 'Sem mesa' }}
                                </div>
                            </td>

                            <td class="px-4 py-3 text-ink-600 dark:text-ink-300">
                                {{ $pedido->cliente->nome ?? 'Consumidor' }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @foreach(($pedido->pagamentos ?? []) as $pag)
                                        <span class="inline-block px-2 py-1 rounded text-xs bg-ink-100 dark:bg-ink-700 text-ink-700 dark:text-ink-300">
                                            {{ str_replace('_', ' ', $pag['forma']) }}
                                            — R$ {{ number_format($pag['valor'], 2, ',', '.') }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>

                            <td class="px-4 py-3 text-right font-semibold text-ink-900 dark:text-ink-50">
                                R$ {{ number_format($pedido->total, 2, ',', '.') }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="verVenda({{ $pedido->id }})"
                                            class="px-3 py-1.5 border border-gray-300 dark:border-ink-600 rounded-lg text-xs font-medium hover:bg-ink-50 dark:hover:bg-ink-700">
                                        Ver
                                    </button>

                                    <button
    wire:click="abrirCupom({{ $pedido->id }})"
    class="px-3 py-1.5 bg-ink-900 dark:bg-ink-100 text-white dark:text-ink-900 rounded-lg text-xs font-medium hover:bg-ink-800"
>
    Cupom
</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-ink-400">
                                Nenhuma venda encontrada
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200 dark:border-ink-700">
            {{ $this->pedidos->links() }}
        </div>
    </div>
    @if($mostrarCupom && $pedidoCupom)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60" wire:click="fecharCupom"></div>

        <div class="relative bg-white dark:bg-ink-900 rounded-xl shadow-xl w-full max-w-3xl max-h-[90vh] overflow-hidden border border-gray-200 dark:border-ink-700">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-ink-700">
                <div>
                    <h2 class="text-lg font-semibold text-ink-900 dark:text-ink-50">
                        Cupom #{{ $pedidoCupom->numero_pedido }}
                    </h2>
                    <p class="text-sm text-ink-500 dark:text-ink-400">
                        {{ $pedidoCupom->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        onclick="imprimirCupom()"
                        class="px-3 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-sm font-medium hover:bg-ink-50 dark:hover:bg-ink-800"
                    >
                        Imprimir
                    </button>

                  <a href="{{ route('tenant.vendas.cupom.pdf', ['id' => $pedidoCupom->id]) }}">
    Baixar PDF
</a>

                    <button
                        wire:click="fecharCupom"
                        class="px-3 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-sm font-medium hover:bg-ink-50 dark:hover:bg-ink-800"
                    >
                        Fechar
                    </button>
                </div>
            </div>

            <div class="p-6 overflow-y-auto max-h-[calc(90vh-76px)] bg-ink-50 dark:bg-ink-950">
                <div id="cupom-print-area" class="mx-auto bg-white text-black shadow rounded-lg p-6" style="width: 320px; font-family: monospace; font-size: 12px;">
                    <div class="text-center">
                        <div style="font-weight: bold;">{{ tenant()->name }}</div>
                        <div>Cupom não fiscal</div>
                    </div>

                    <hr style="border-top:1px dashed #000; margin:8px 0;">

                    <div>Pedido: #{{ $pedidoCupom->numero_pedido }}</div>
                    <div>Data: {{ $pedidoCupom->created_at->format('d/m/Y H:i') }}</div>
                    <div>Tipo: {{ ucfirst($pedidoCupom->tipo) }}</div>
                    <div>Mesa: {{ $pedidoCupom->mesa ?: '-' }}</div>
                    <div>Cliente: {{ $pedidoCupom->cliente->nome ?? 'Consumidor' }}</div>

                    <hr style="border-top:1px dashed #000; margin:8px 0;">

                    @foreach($pedidoCupom->itens as $item)
                        <div>{{ $item->produto_nome }}</div>
                        <div style="display:flex; justify-content:space-between;">
                            <span>
                                {{ number_format($item->quantidade, $item->quantidade == intval($item->quantidade) ? 0 : 3, ',', '.') }}
                                x {{ number_format($item->preco_unitario, 2, ',', '.') }}
                            </span>
                            <span>{{ number_format($item->subtotal, 2, ',', '.') }}</span>
                        </div>
                    @endforeach

                    <hr style="border-top:1px dashed #000; margin:8px 0;">

                    @foreach(($pedidoCupom->pagamentos ?? []) as $pag)
                        <div style="display:flex; justify-content:space-between;">
                            <span>{{ str_replace('_', ' ', $pag['forma']) }}</span>
                            <span>{{ number_format($pag['valor'], 2, ',', '.') }}</span>
                        </div>
                    @endforeach

                    <hr style="border-top:1px dashed #000; margin:8px 0;">

                    <div style="display:flex; justify-content:space-between; font-weight:bold;">
                        <span>TOTAL</span>
                        <span>R$ {{ number_format($pedidoCupom->total, 2, ',', '.') }}</span>
                    </div>

                    <hr style="border-top:1px dashed #000; margin:8px 0;">

                    <div class="text-center">
                        Obrigado pela preferência
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@push('scripts')
<script>
    function imprimirCupom() {
        const conteudo = document.getElementById('cupom-print-area')?.innerHTML;
        if (!conteudo) return;

        const janela = window.open('', '_blank', 'width=400,height=700');
        janela.document.write(`
            <html>
                <head>
                    <title>Cupom</title>
                    <style>
                        body {
                            font-family: monospace;
                            width: 320px;
                            margin: 0 auto;
                            padding: 12px;
                            font-size: 12px;
                            color: #000;
                        }
                    </style>
                </head>
                <body>${conteudo}</body>
            </html>
        `);
        janela.document.close();
        janela.focus();
        janela.print();
    }
</script>
@endpush
</div>