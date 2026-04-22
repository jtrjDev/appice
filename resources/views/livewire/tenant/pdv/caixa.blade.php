<div
    x-data="{}"
    x-on:pdv-sucesso.window="alert($event.detail.mensagem)"
    x-on:pdv-aviso.window="alert($event.detail.mensagem)"
>
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold text-ink-900 dark:text-ink-50">Controle de Caixa</h1>
            <p class="text-sm text-ink-500 dark:text-ink-400">Abertura, acompanhamento e fechamento</p>
        </div>
    </div>

    {{-- ═══════════════════════════════════
         CAIXA FECHADO — tela de abertura
    ════════════════════════════════════ --}}
    @if(!$this->caixa)
        <div class="max-w-md mx-auto mt-20 text-center">
            <div class="bg-white dark:bg-ink-900 border border-gray-200 dark:border-ink-700 rounded-xl p-8 space-y-5">
                <div class="size-16 bg-ink-100 dark:bg-ink-800 rounded-full flex items-center justify-center mx-auto">
                    <svg class="size-8 text-ink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a5 5 0 00-10 0v2M5 9h14l1 11H4L5 9z"/>
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-ink-900 dark:text-ink-50">Nenhum caixa aberto</h2>
                <p class="text-sm text-ink-500">Informe o saldo inicial para abrir o caixa.</p>

                <div x-data="{ saldo: 0 }">
                    <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1 text-left">
                        Saldo inicial (R$)
                    </label>
                    <input
                        type="number"
                        x-model="saldo"
                        step="0.01"
                        min="0"
                        placeholder="0,00"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-center text-lg font-semibold dark:bg-ink-800 dark:text-ink-100 focus:outline-none focus:ring-2 focus:ring-ink-500"
                    >
                    <button
                        x-on:click="$wire.abrirCaixa(parseFloat(saldo))"
                        class="mt-4 w-full py-3 bg-ink-900 dark:bg-ink-100 text-white dark:text-ink-900 rounded-lg font-medium hover:bg-ink-800 transition-colors"
                    >
                        Abrir Caixa
                    </button>
                </div>
            </div>
        </div>

    {{-- ═══════════════════════════════════
         CAIXA ABERTO — dashboard
    ════════════════════════════════════ --}}
    @else
        {{-- Cards de totais --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-ink-900 border border-gray-200 dark:border-ink-700 rounded-xl p-4">
                <p class="text-xs text-ink-500 mb-1">Total Vendas</p>
                <p class="text-2xl font-bold text-ink-900 dark:text-ink-50">
                    R$ {{ number_format($this->caixa->total_vendas, 2, ',', '.') }}
                </p>
                <p class="text-xs text-ink-400 mt-1">{{ $this->caixa->quantidade_vendas }} pedidos</p>
            </div>

            <div class="bg-white dark:bg-ink-900 border border-gray-200 dark:border-ink-700 rounded-xl p-4">
                <p class="text-xs text-ink-500 mb-1">Dinheiro</p>
                <p class="text-2xl font-bold text-green-600">
                    R$ {{ number_format($this->totalPorForma['dinheiro'], 2, ',', '.') }}
                </p>
                <p class="text-xs text-ink-400 mt-1">
                    Saldo esperado: R$ {{ number_format($this->saldoEsperado, 2, ',', '.') }}
                </p>
            </div>

            <div class="bg-white dark:bg-ink-900 border border-gray-200 dark:border-ink-700 rounded-xl p-4">
                <p class="text-xs text-ink-500 mb-1">Cartão</p>
                <p class="text-2xl font-bold text-blue-600">
                    R$ {{ number_format($this->totalPorForma['cartao_credito'] + $this->totalPorForma['cartao_debito'], 2, ',', '.') }}
                </p>
                <p class="text-xs text-ink-400 mt-1">
                    Créd: R$ {{ number_format($this->totalPorForma['cartao_credito'], 2, ',', '.') }} |
                    Déb: R$ {{ number_format($this->totalPorForma['cartao_debito'], 2, ',', '.') }}
                </p>
            </div>

            <div class="bg-white dark:bg-ink-900 border border-gray-200 dark:border-ink-700 rounded-xl p-4">
                <p class="text-xs text-ink-500 mb-1">PIX</p>
                <p class="text-2xl font-bold text-purple-600">
                    R$ {{ number_format($this->totalPorForma['pix'], 2, ',', '.') }}
                </p>
                <p class="text-xs text-ink-400 mt-1">
                    Aberto às {{ $this->caixa->aberto_em->format('H:i') }}
                </p>
            </div>
        </div>

        {{-- Tabela de pedidos --}}
        <div class="bg-white dark:bg-ink-900 border border-gray-200 dark:border-ink-700 rounded-xl mb-6">
            <div class="p-4 border-b border-gray-200 dark:border-ink-700">
                <h3 class="font-semibold text-ink-900 dark:text-ink-50">Pedidos do caixa</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-ink-50 dark:bg-ink-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-ink-500 uppercase">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-ink-500 uppercase">Horário</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-ink-500 uppercase">Itens</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-ink-500 uppercase">Pagamento</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-ink-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-ink-700">
                        @forelse($this->pedidosHoje as $pedido)
                            <tr class="hover:bg-ink-50 dark:hover:bg-ink-800">
                                <td class="px-4 py-3 font-mono text-ink-600 dark:text-ink-300">
                                    #{{ $pedido->numero_pedido }}
                                </td>
                                <td class="px-4 py-3 text-ink-500">
                                    {{ $pedido->created_at->format('H:i') }}
                                </td>
                                <td class="px-4 py-3 text-ink-700 dark:text-ink-300">
                                    {{ $pedido->itens->count() }} item(s) —
                                    {{ $pedido->itens->pluck('produto_nome')->join(', ') }}
                                </td>
                                <td class="px-4 py-3">
                                    @foreach($pedido->pagamentos as $pag)
                                        <span class="inline-block px-2 py-0.5 rounded text-xs bg-ink-100 dark:bg-ink-700 text-ink-600 dark:text-ink-300 capitalize">
                                            {{ str_replace('_', ' ', $pag['forma']) }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-ink-900 dark:text-ink-50">
                                    R$ {{ number_format($pedido->total, 2, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-ink-400">
                                    Nenhum pedido neste caixa ainda
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Botão fechar caixa --}}
        <div class="flex justify-end">
            <button
                wire:click="abrirModalFechamento"
                class="px-6 py-3 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors"
            >
                Fechar Caixa
            </button>
        </div>
    @endif

    {{-- ═══════════════════════════════════
         MODAL FECHAMENTO
    ════════════════════════════════════ --}}
    @if($mostrarFechamento)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60" wire:click="$set('mostrarFechamento', false)"></div>
            <div class="relative bg-white dark:bg-ink-900 rounded-xl shadow-xl w-full max-w-md p-6 space-y-4">

                <h2 class="text-xl font-semibold text-ink-900 dark:text-ink-50">Fechar Caixa</h2>

                {{-- Resumo --}}
                <div class="space-y-2 bg-ink-50 dark:bg-ink-800 rounded-lg p-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-ink-600 dark:text-ink-300">Saldo inicial</span>
                        <span class="font-medium">R$ {{ number_format($this->caixa->saldo_inicial, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-ink-600 dark:text-ink-300">Total em dinheiro</span>
                        <span class="font-medium text-green-600">+ R$ {{ number_format($this->totalPorForma['dinheiro'], 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between border-t border-gray-200 dark:border-ink-600 pt-2 mt-2">
                        <span class="font-semibold text-ink-900 dark:text-ink-50">Saldo esperado</span>
                        <span class="font-bold">R$ {{ number_format($this->saldoEsperado, 2, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Saldo informado --}}
                <div>
                    <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">
                        Saldo contado no caixa (R$)
                    </label>
                    <input
                        type="number"
                        wire:model.live="saldoFinalInformado"
                        step="0.01"
                        min="0"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-lg font-semibold dark:bg-ink-800 dark:text-ink-100 focus:outline-none focus:ring-2 focus:ring-ink-500"
                    >
                </div>

                {{-- Diferença --}}
                <div class="flex justify-between items-center px-4 py-3 rounded-lg
                    {{ $this->diferenca == 0 ? 'bg-green-50 dark:bg-green-900/20 border border-green-200' :
                       ($this->diferenca > 0 ? 'bg-blue-50 dark:bg-blue-900/20 border border-blue-200' :
                       'bg-red-50 dark:bg-red-900/20 border border-red-200') }}">
                    <span class="text-sm font-medium">
                        {{ $this->diferenca == 0 ? 'Caixa fechado ✓' : ($this->diferenca > 0 ? 'Sobra' : 'Falta') }}
                    </span>
                    <span class="font-bold text-lg
                        {{ $this->diferenca == 0 ? 'text-green-600' : ($this->diferenca > 0 ? 'text-blue-600' : 'text-red-600') }}">
                        R$ {{ number_format(abs($this->diferenca), 2, ',', '.') }}
                    </span>
                </div>

                {{-- Observação --}}
                <div>
                    <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">
                        Observação (opcional)
                    </label>
                    <textarea
                        wire:model="observacao"
                        rows="2"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-sm dark:bg-ink-800 dark:text-ink-100 focus:outline-none focus:ring-2 focus:ring-ink-500"
                        placeholder="Alguma observação sobre o fechamento..."
                    ></textarea>
                </div>

                {{-- Botões --}}
                <div class="flex gap-3 pt-1">
                    <button
                        wire:click="$set('mostrarFechamento', false)"
                        class="flex-1 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-sm font-medium hover:bg-ink-50 dark:hover:bg-ink-800 transition-colors"
                    >
                        Cancelar
                    </button>
                    <button
                        wire:click="fecharCaixa"
                        class="flex-1 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors"
                    >
                        Confirmar Fechamento
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>