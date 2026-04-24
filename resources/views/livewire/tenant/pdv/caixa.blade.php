<div x-data="{}"
    x-on:toast.window="console.log($event.detail)">
   
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
        {{-- Cards de totais --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    {{-- Card Total Vendas --}}
    <div class="bg-white dark:bg-ink-900 border border-gray-200 dark:border-ink-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <p class="text-sm text-ink-500">Total Vendas</p>
            <svg class="size-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <p class="text-2xl font-bold text-ink-900 dark:text-ink-50">
            R$ {{ number_format($this->caixa->total_vendas, 2, ',', '.') }}
        </p>
        <p class="text-xs text-ink-400 mt-1">{{ $this->caixa->quantidade_vendas }} pedidos</p>
    </div>

    {{-- Card Dinheiro --}}
    <div class="bg-white dark:bg-ink-900 border border-gray-200 dark:border-ink-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <p class="text-sm text-ink-500">Dinheiro</p>
            <svg class="size-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v4m0 12v4M4.93 4.93l2.83 2.83m8.48 8.48l2.83 2.83M2 12h4m12 0h4M4.93 19.07l2.83-2.83m8.48-8.48l2.83-2.83" />
            </svg>
        </div>
        <p class="text-2xl font-bold text-green-600">
            R$ {{ number_format($this->totalPorForma['dinheiro'], 2, ',', '.') }}
        </p>
        <p class="text-xs text-ink-400 mt-1">
            {{ $this->caixa->quantidade_vendas > 0 ? round(($this->totalPorForma['dinheiro'] / $this->caixa->total_vendas) * 100, 1) : 0 }}% das vendas
        </p>
    </div>

    {{-- Card Cartão (Crédito + Débito) --}}
    <div class="bg-white dark:bg-ink-900 border border-gray-200 dark:border-ink-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <p class="text-sm text-ink-500">Cartões</p>
            <svg class="size-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
            </svg>
        </div>
        <p class="text-2xl font-bold text-blue-600">
            R$ {{ number_format($this->totalPorForma['cartao_credito'] + $this->totalPorForma['cartao_debito'], 2, ',', '.') }}
        </p>
        <div class="flex justify-between text-xs text-ink-400 mt-1">
            <span>Crédito: R$ {{ number_format($this->totalPorForma['cartao_credito'], 2, ',', '.') }}</span>
            <span>Débito: R$ {{ number_format($this->totalPorForma['cartao_debito'], 2, ',', '.') }}</span>
        </div>
    </div>

    {{-- Card PIX --}}
    <div class="bg-white dark:bg-ink-900 border border-gray-200 dark:border-ink-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <p class="text-sm text-ink-500">PIX</p>
            <svg class="size-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
        </div>
        <p class="text-2xl font-bold text-purple-600">
            R$ {{ number_format($this->totalPorForma['pix'], 2, ',', '.') }}
        </p>
        <p class="text-xs text-ink-400 mt-1">
            Aberto às {{ $this->caixa->aberto_em->format('H:i') }}
        </p>
    </div>
</div>

{{-- Cards de Resumo Financeiro --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    {{-- Card Saldo Inicial --}}
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <p class="text-sm text-blue-700 dark:text-blue-400">Saldo Inicial</p>
            <svg class="size-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <p class="text-2xl font-bold text-blue-700 dark:text-blue-400">
            R$ {{ number_format($this->caixa->saldo_inicial, 2, ',', '.') }}
        </p>
        <p class="text-xs text-blue-600 dark:text-blue-500 mt-1">Valor de abertura do caixa</p>
    </div>

    {{-- Card Sangrias (Saídas) --}}
    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <p class="text-sm text-red-700 dark:text-red-400">Sangrias</p>
            <svg class="size-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
            </svg>
        </div>
        <p class="text-2xl font-bold text-red-600 dark:text-red-400">
            R$ {{ number_format($this->totalSangrias, 2, ',', '.') }}
        </p>
        <p class="text-xs text-red-600 dark:text-red-500 mt-1">Total retirado do caixa</p>
    </div>

    {{-- Card Suprimentos (Entradas) --}}
    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <p class="text-sm text-green-700 dark:text-green-400">Suprimentos</p>
            <svg class="size-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
            </svg>
        </div>
        <p class="text-2xl font-bold text-green-600 dark:text-green-400">
            R$ {{ number_format($this->totalSuprimentos, 2, ',', '.') }}
        </p>
        <p class="text-xs text-green-600 dark:text-green-500 mt-1">Total adicionado ao caixa (troco)</p>
    </div>

    {{-- Card Saldo Esperado --}}
    <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-xl p-4">
        <div class="flex items-center justify-between mb-2">
            <p class="text-sm text-purple-700 dark:text-purple-400">Saldo Esperado</p>
            <svg class="size-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        </div>
        <p class="text-2xl font-bold text-purple-700 dark:text-purple-400">
            R$ {{ number_format($this->saldoEsperado, 2, ',', '.') }}
        </p>
        <p class="text-xs text-purple-600 dark:text-purple-500 mt-1">
            Saldo inicial + Vendas - Sangrias + Suprimentos
        </p>
    </div>
</div>

{{-- Botões de Sangria/Suprimento --}}
<div class="grid grid-cols-2 gap-3 mb-6">
    <button wire:click="abrirModalSangria('sangria')" 
        class="px-4 py-3 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors flex items-center justify-center gap-2">
        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
        </svg>
        Sangria (Retirada)
    </button>
    <button wire:click="abrirModalSangria('suprimento')" 
        class="px-4 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
        Suprimento (Troco)
    </button>
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
                            <th class="px-4 py-3 text-center text-xs font-medium text-ink-500 uppercase">Ações</th>
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
                                        <span class="inline-block px-2 py-0.5 rounded text-xs bg-ink-100 dark:bg-ink-700 text-ink-600 dark:text-ink-300 capitalize mr-1 mb-1">
                                            {{ str_replace('_', ' ', $pag['forma']) }} — R$ {{ number_format($pag['valor'], 2, ',', '.') }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-ink-900 dark:text-ink-50">
                                    R$ {{ number_format($pedido->total, 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a wire:navigate href="{{ route('tenant.vendas.show', ['id' => $pedido->id]) }}"
                                            class="px-3 py-1.5 border border-gray-300 dark:border-ink-600 rounded-lg text-xs font-medium hover:bg-ink-50 dark:hover:bg-ink-700">
                                                Ver
                                        </a>
                                        <a href="{{ route('tenant.vendas.cupom', ['id' => $pedido->id]) }}"
                                        target="_blank"
                                        class="px-3 py-1.5 bg-ink-900 dark:bg-ink-100 text-white dark:text-ink-900 rounded-lg text-xs font-medium hover:bg-ink-800">
                                            Cupom
                                        </a>
                                    </div>
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
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(0,0,0,0.7);">
        <div class="relative bg-white dark:bg-ink-900 rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto p-6 space-y-4">

            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-ink-900 dark:text-ink-50">Fechar Caixa</h2>
                <button wire:click="$set('mostrarFechamento', false)" class="text-ink-500 hover:text-ink-700">
                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Resumo completo --}}
            <div class="space-y-3 bg-ink-50 dark:bg-ink-800 rounded-lg p-4 text-sm">
                <div class="flex justify-between">
                    <span class="text-ink-600 dark:text-ink-300">Saldo inicial</span>
                    <span class="font-medium">R$ {{ number_format($this->caixa->saldo_inicial, 2, ',', '.') }}</span>
                </div>
                
                <div class="border-t border-gray-200 dark:border-ink-600 pt-2">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-ink-600 dark:text-ink-300">Vendas por forma de pagamento:</span>
                    </div>
                    <div class="space-y-1 pl-2">
                        <div class="flex justify-between">
                            <span>💰 Dinheiro</span>
                            <span class="font-medium text-green-600">+ R$ {{ number_format($this->totalPorForma['dinheiro'], 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>💳 Cartão Crédito</span>
                            <span class="font-medium text-blue-600">+ R$ {{ number_format($this->totalPorForma['cartao_credito'], 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>💳 Cartão Débito</span>
                            <span class="font-medium text-blue-600">+ R$ {{ number_format($this->totalPorForma['cartao_debito'], 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>📱 PIX</span>
                            <span class="font-medium text-purple-600">+ R$ {{ number_format($this->totalPorForma['pix'], 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                
                @if($this->totalSangrias > 0)
                <div class="border-t border-red-200 pt-2">
                    <div class="flex justify-between">
                        <span class="text-red-600">💸 Sangrias</span>
                        <span class="font-medium text-red-600">- R$ {{ number_format($this->totalSangrias, 2, ',', '.') }}</span>
                    </div>
                </div>
                @endif

                @if($this->totalSuprimentos > 0)
                <div class="border-t border-blue-200 pt-2">
                    <div class="flex justify-between">
                        <span class="text-blue-600">💰 Suprimentos</span>
                        <span class="font-medium text-blue-600">+ R$ {{ number_format($this->totalSuprimentos, 2, ',', '.') }}</span>
                    </div>
                </div>
                @endif

                <div class="border-t-2 border-gray-300 dark:border-ink-600 pt-2 mt-2">
                    <div class="flex justify-between">
                        <span class="font-bold text-ink-900 dark:text-ink-50 text-base">Saldo esperado</span>
                        <span class="font-bold text-lg">R$ {{ number_format($this->saldoEsperado, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-xs text-ink-500 mt-1">
                        <span>Cálculo: Saldo inicial + Vendas - Sangrias + Suprimentos</span>
                    </div>
                </div>
            </div>

            {{-- Saldo informado --}}
            <div>
                <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">
                    Saldo contado no caixa (R$)
                </label>
                <input type="number" wire:model.live="saldoFinalInformado" step="0.01" min="0"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-lg font-semibold dark:bg-ink-800 dark:text-ink-100 focus:outline-none focus:ring-2 focus:ring-ink-500">
            </div>

            {{-- Diferença --}}
            <div class="flex justify-between items-center px-4 py-3 rounded-lg
                {{ $this->diferenca == 0 ? 'bg-green-50 dark:bg-green-900/20 border border-green-200' :
                   ($this->diferenca > 0 ? 'bg-blue-50 dark:bg-blue-900/20 border border-blue-200' :
                   'bg-red-50 dark:bg-red-900/20 border border-red-200') }}">
                <span class="text-sm font-medium">
                    {{ $this->diferenca == 0 ? '✓ Caixa confere' : ($this->diferenca > 0 ? '💰 Sobra no caixa' : '⚠️ Falta no caixa') }}
                </span>
                <span class="font-bold text-lg
                    {{ $this->diferenca == 0 ? 'text-green-600' : ($this->diferenca > 0 ? 'text-blue-600' : 'text-red-600') }}">
                    R$ {{ number_format(abs($this->diferenca), 2, ',', '.') }}
                </span>
            </div>

            @if($this->diferenca != 0)
            <div class="text-xs text-ink-500 text-center">
                {{ $this->diferenca > 0 ? 'Verifique se há valores esquecidos ou trocos não contados.' : 'Confira se todos os pagamentos foram registrados.' }}
            </div>
            @endif

            {{-- Observação --}}
            <div>
                <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">
                    Observação (opcional)
                </label>
                <textarea wire:model="observacao" rows="2"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-sm dark:bg-ink-800 dark:text-ink-100 focus:outline-none focus:ring-2 focus:ring-ink-500"
                    placeholder="Alguma observação sobre o fechamento..."></textarea>
            </div>

            {{-- Botões --}}
            <div class="flex gap-3 pt-1">
                <button wire:click="$set('mostrarFechamento', false)"
                    class="flex-1 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-sm font-medium hover:bg-ink-50 dark:hover:bg-ink-800 transition-colors">
                    Cancelar
                </button>
                <button wire:click="fecharCaixa"
                    class="flex-1 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors">
                    Confirmar Fechamento
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════
         MODAL SANGRIA/SUPRIMENTO
    ════════════════════════════════════ --}}
    @if($mostrarModalSangria)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(0,0,0,0.7);">
        <div class="relative bg-white dark:bg-ink-900 rounded-xl shadow-xl w-full max-w-md p-6 space-y-4">

            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-ink-900 dark:text-ink-50">
                    {{ $tipoSangria == 'sangria' ? 'Sangria (Retirada)' : 'Suprimento (Entrada de troco)' }}
                </h2>
                <button wire:click="$set('mostrarModalSangria', false)" class="text-ink-500 hover:text-ink-700">
                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div>
                <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Valor (R$)</label>
                <input type="number" step="0.01" wire:model="valorSangria" 
                    class="w-full px-3 py-2 border border-gray-300 dark:border-ink-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-ink-500">
                @error('valorSangria') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Motivo</label>
                <input type="text" wire:model="motivoSangria" 
                    class="w-full px-3 py-2 border border-gray-300 dark:border-ink-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-ink-500" 
                    placeholder="Ex: Troco para caixa, pagamento de fornecedor...">
                @error('motivoSangria') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3 pt-4">
                <button wire:click="$set('mostrarModalSangria', false)"
                    class="flex-1 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-sm font-medium hover:bg-ink-50 dark:hover:bg-ink-800 transition-colors">
                    Cancelar
                </button>
                <button wire:click="salvarSangria"
                    class="flex-1 py-2 bg-ink-900 text-white rounded-lg text-sm font-medium hover:bg-ink-800 transition-colors">
                    Confirmar
                </button>
            </div>
        </div>
    </div>
    @endif
</div>