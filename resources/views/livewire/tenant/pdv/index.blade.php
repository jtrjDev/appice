<div
    x-data="{}"
    x-on:pdv-sucesso.window="alert($event.detail.mensagem)"
    x-on:pdv-aviso.window="alert($event.detail.mensagem)">
    {{-- Cabeçalho --}}
    <div class="mb-4 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold text-ink-900 dark:text-ink-50">Ponto de Venda</h1>
            <p class="text-sm text-ink-500 dark:text-ink-400">Sistema rápido de vendas</p>
        </div>
        <div class="text-right">
            <p class="text-xs text-ink-500">Atalhos: F2 (Código) | F5 (Finalizar) | F6 (Novo)</p>
        </div>
    </div>
    {{-- Status do Caixa --}}
    @if($this->caixaAberto)
    <div class="mb-4 flex items-center gap-3 px-4 py-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-sm">
        <span class="size-2 rounded-full bg-green-500 animate-pulse inline-block"></span>
        <span class="font-medium text-green-700 dark:text-green-400">Caixa aberto</span>
        <span class="text-green-600 dark:text-green-500">•</span>
        <span class="text-green-600 dark:text-green-500">Abertura: {{ $this->caixaAberto->aberto_em->format('d/m/Y H:i') }}</span>
        <span class="text-green-600 dark:text-green-500">•</span>
        <span class="text-green-600 dark:text-green-500">Operador: {{ $this->caixaAberto->operador->name ?? 'N/A' }}</span>
        <span class="text-green-600 dark:text-green-500">•</span>
        <span class="text-green-600 dark:text-green-500">Vendas: {{ $this->caixaAberto->quantidade_vendas }}</span>
        <span class="text-green-600 dark:text-green-500">•</span>
        <span class="font-medium text-green-700 dark:text-green-400">Total: R$ {{ number_format($this->caixaAberto->total_vendas, 2, ',', '.') }}</span>
    </div>
    @else
    <div class="mb-4 flex items-center justify-between px-4 py-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-sm">
        <div class="flex items-center gap-3">
            <span class="size-2 rounded-full bg-red-500 inline-block"></span>
            <span class="font-medium text-red-700 dark:text-red-400">Nenhum caixa aberto</span>
            <span class="text-red-600 dark:text-red-500">— Abra o caixa para iniciar as vendas</span>
        </div>
        <a href="{{ route('tenant.caixa') }}"
            class="px-3 py-1 bg-red-600 text-white rounded-lg text-xs font-medium hover:bg-red-700 transition-colors">
            Abrir Caixa
        </a>
    </div>
    @endif
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ═══════════════════════════════════════════
             ÁREA DE PRODUTOS
        ════════════════════════════════════════════ --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Barra de busca, código e quantidade --}}
            <div class="grid grid-cols-12 gap-3">
                {{-- Busca por nome --}}
                <div class="col-span-5 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-ink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                    </svg>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="busca"
                        placeholder="Buscar produto por nome..."
                        class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-ink-500 dark:bg-ink-800 dark:border-ink-600 dark:text-ink-100">
                </div>

                {{-- Código de barras --}}
                <div class="col-span-4 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-ink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h1m1 0h1M4 12h1m1 0h1M4 18h1m1 0h1M15 6h1m1 0h1M15 12h1m1 0h1M15 18h1m1 0h1M9 3v18M12 3v18" />
                    </svg>
                    <input
                        type="text"
                        wire:model="codigoProduto"
                        wire:keydown.enter="buscarPorCodigo"
                        placeholder="Código / ID"
                        class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-ink-500 dark:bg-ink-800 dark:border-ink-600 dark:text-ink-100"
                        id="campo-codigo"
                        autofocus>
                </div>

                {{-- Quantidade --}}
                <div class="col-span-3 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-ink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                    </svg>
                    <input
                        type="text"
                        placeholder="1"
                        id="campo-quantidade"
                        x-data="{ raw: '' }"
                        x-on:keydown.enter="
        let num = raw === '' ? 1 : parseInt(raw) / 1000;
        $wire.set('quantidadeInput', num).then(() => {
            document.getElementById('campo-codigo').dispatchEvent(new KeyboardEvent('keydown', { key: 'Enter', bubbles: true }));
        });
    "
                        x-on:keydown="
        if (event.key === 'Enter') return;
        if (event.key === 'Backspace') {
            raw = raw.slice(0, -1);
        } else if (event.key >= '0' && event.key <= '9') {
            raw = raw + event.key;
        } else {
            return;
        }
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
                        class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-ink-500 dark:bg-ink-800 dark:border-ink-600 dark:text-ink-100">
                </div>
            </div>

            {{-- Categorias --}}
            <div class="flex gap-2 overflow-x-auto pb-2">
                <button
                    wire:click="selecionarCategoria(null)"
                    class="px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition-colors
                           {{ !$categoriaSelecionada ? 'bg-ink-900 text-white dark:bg-ink-100 dark:text-ink-900' : 'bg-ink-100 text-ink-700 hover:bg-ink-200 dark:bg-ink-800 dark:text-ink-300' }}">
                    TODOS
                </button>
                @foreach($categorias as $categoria)
                <button
                    wire:click="selecionarCategoria({{ $categoria->id }})"
                    class="px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition-colors
                               {{ $categoriaSelecionada == $categoria->id ? 'bg-ink-900 text-white dark:bg-ink-100 dark:text-ink-900' : 'bg-ink-100 text-ink-700 hover:bg-ink-200 dark:bg-ink-800 dark:text-ink-300' }}">
                    {{ $categoria->nome }}
                </button>
                @endforeach
            </div>

            {{-- Grid de Produtos --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 max-h-[500px] overflow-y-auto pr-1">
                @forelse($produtos as $produto)
                <button
                    wire:click="adicionarProduto({{ $produto->id }})"
                    wire:key="prod-{{ $produto->id }}"
                    class="group bg-white dark:bg-ink-900 border border-gray-200 dark:border-ink-700 rounded-lg p-3 text-left hover:border-ink-400 hover:shadow-md transition-all">
                    <div class="flex flex-col gap-1">
                        <div class="w-full h-16 bg-ink-100 dark:bg-ink-800 rounded flex items-center justify-center mb-1">
                            <svg class="size-7 text-ink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7H4a1 1 0 00-1 1v10a1 1 0 001 1h16a1 1 0 001-1V8a1 1 0 00-1-1zM16 3H8l-1 4h10l-1-4z" />
                            </svg>
                        </div>
                        <p class="font-medium text-sm line-clamp-2 text-ink-800 dark:text-ink-100">{{ $produto->nome }}</p>
                        <p class="text-base font-bold text-primary-600">
                            R$ {{ number_format($produto->preco_atual, 2, ',', '.') }}
                        </p>
                        @if($produto->codigo)
                        <p class="text-xs text-ink-400 font-mono">{{ $produto->codigo }}</p>
                        @endif
                    </div>
                </button>
                @empty
                <div class="col-span-full text-center py-12 text-ink-400">
                    <svg class="size-10 mx-auto mb-2 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                    </svg>
                    <p class="text-sm">Nenhum produto encontrado</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- ═══════════════════════════════════════════
             CARRINHO
        ════════════════════════════════════════════ --}}
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-ink-900 rounded-lg border border-gray-200 dark:border-ink-700 sticky top-20">

                {{-- Cabeçalho do carrinho --}}
                <div class="p-4 border-b border-gray-200 dark:border-ink-700">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="font-semibold text-ink-900 dark:text-ink-50">
                            Carrinho
                            <span class="text-sm font-normal text-ink-500">({{ number_format($this->totalItens, 0) }} itens)</span>
                        </h3>
                        @if(count($carrinho) > 0)
                        <button
                            wire:click="limparCarrinho"
                            wire:confirm="Deseja limpar o carrinho?"
                            class="text-xs text-red-500 hover:text-red-700">
                            Limpar
                        </button>
                        @endif
                    </div>

                    {{-- Mesa e Cliente --}}
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-xs text-ink-500">Mesa/Comanda</label>
                            <input
                                type="text"
                                wire:model.live.debounce.500ms="mesa"
                                class="w-full mt-1 px-2 py-1 text-sm border border-gray-300 rounded-lg dark:bg-ink-800 dark:border-ink-600 dark:text-ink-100"
                                placeholder="Nº mesa">
                        </div>
                        <div>
                            <label class="text-xs text-ink-500">Cliente</label>
                            <select
                                wire:model="clienteId"
                                class="w-full mt-1 px-2 py-1 text-sm border border-gray-300 rounded-lg dark:bg-ink-800 dark:border-ink-600 dark:text-ink-100">
                                <option value="">Consumidor</option>
                                @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Itens do carrinho --}}
                <div class="max-h-[380px] overflow-y-auto divide-y divide-gray-100 dark:divide-ink-700">
                    @forelse($carrinho as $chave => $item)
                    <div class="p-3 hover:bg-ink-50 dark:hover:bg-ink-800" wire:key="item-{{ $chave }}">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1 min-w-0 pr-2">
                                <p class="text-sm font-medium text-ink-900 dark:text-ink-100 truncate">{{ $item['nome'] }}</p>
                                <p class="text-xs text-ink-500">{{ $item['preco_formatado'] }}</p>
                            </div>
                            <button
                                wire:click="removerProduto({{ $item['id'] }})"
                                class="text-red-400 hover:text-red-600 flex-shrink-0">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m-7 0a1 1 0 01-1-1V5h10v1a1 1 0 01-1 1H9z" />
                                </svg>
                            </button>
                        </div>
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-1">
                                <button
                                    wire:click="atualizarQuantidade({{ $item['id'] }}, {{ $item['quantidade'] - 1 }})"
                                    class="size-6 rounded bg-ink-100 hover:bg-ink-200 dark:bg-ink-700 dark:hover:bg-ink-600 text-ink-700 dark:text-ink-100 text-base leading-none flex items-center justify-center">−</button>
                                <span class="w-10 text-center text-sm font-medium text-ink-800 dark:text-ink-100">
                                    {{ number_format($item['quantidade'], $item['quantidade'] == intval($item['quantidade']) ? 0 : 3, ',', '.') }}
                                </span>
                                <button
                                    wire:click="atualizarQuantidade({{ $item['id'] }}, {{ $item['quantidade'] + 1 }})"
                                    class="size-6 rounded bg-ink-100 hover:bg-ink-200 dark:bg-ink-700 dark:hover:bg-ink-600 text-ink-700 dark:text-ink-100 text-base leading-none flex items-center justify-center">+</button>
                            </div>
                            <p class="text-sm font-semibold text-ink-900 dark:text-ink-100">
                                R$ {{ number_format($item['subtotal'], 2, ',', '.') }}
                            </p>
                        </div>
                    </div>
                    @empty
                    <div class="p-10 text-center text-ink-400">
                        <svg class="size-10 mx-auto mb-2 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.4 5.6A1 1 0 006.6 20h10.8a1 1 0 001-1.4L17 13M9 20a1 1 0 100 2 1 1 0 000-2zm8 0a1 1 0 100 2 1 1 0 000-2z" />
                        </svg>
                        <p class="text-sm">Carrinho vazio</p>
                    </div>
                    @endforelse
                </div>

                {{-- Total e botões --}}
                <div class="p-4 border-t border-gray-200 dark:border-ink-700 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-ink-900 dark:text-ink-50">Total</span>
                        <span class="text-lg font-bold text-primary-600">
                            R$ {{ number_format($this->totalCarrinho, 2, ',', '.') }}
                        </span>
                    </div>

                    @if($modoComanda)
                    {{-- Modo mesa: mostrar total restante se comanda já existe --}}
                    @if($comandaId)
                    @php $comanda = \App\Models\Tenant\Comanda::find($comandaId); @endphp
                    @if($comanda && $comanda->total_pago > 0)
                    <div class="flex justify-between text-sm">
                        <span class="text-ink-500">Já pago</span>
                        <span class="text-green-600 font-medium">R$ {{ number_format($comanda->total_pago, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-ink-500">Restante</span>
                        <span class="text-red-600 font-medium">R$ {{ number_format($comanda->total_restante, 2, ',', '.') }}</span>
                    </div>
                    @endif
                    @endif

                    <div class="grid grid-cols-2 gap-2">
                        <button
                            wire:click="salvarComanda"
                            @if(empty($carrinho)) disabled @endif
                            class="py-3 border border-ink-900 dark:border-ink-100 text-ink-900 dark:text-ink-100 rounded-lg font-medium hover:bg-ink-50 dark:hover:bg-ink-800 transition-colors disabled:opacity-40 disabled:cursor-not-allowed text-sm">
                            Salvar Mesa
                        </button>
                        <button
                            wire:click="abrirPagamento"
                            @if(empty($carrinho)) disabled @endif
                            class="py-3 bg-ink-900 dark:bg-ink-100 text-white dark:text-ink-900 rounded-lg font-medium hover:bg-ink-800 transition-colors disabled:opacity-40 disabled:cursor-not-allowed text-sm">
                            Pagar (F5)
                        </button>
                    </div>
                    @else
                    <button
                        wire:click="abrirPagamento"
                        @if(empty($carrinho)) disabled @endif
                        class="w-full py-3 bg-ink-900 dark:bg-ink-100 text-white dark:text-ink-900 rounded-lg font-medium hover:bg-ink-800 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                        Finalizar Venda (F5)
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         MODAL DE PAGAMENTO
    ════════════════════════════════════════════ --}}
    @if($mostrarPagamento)
    <div
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        x-data
        x-on:keydown.escape.window="$wire.fecharModalPagamento()">
        {{-- Overlay --}}
        <div
            class="absolute inset-0 bg-black/60"
            wire:click="fecharModalPagamento"></div>

        {{-- Painel --}}
        <div class="relative bg-white dark:bg-ink-900 rounded-xl shadow-xl w-full max-w-lg p-6 space-y-5">

            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold text-ink-900 dark:text-ink-50">Pagamento</h2>
                <button wire:click="fecharModalPagamento" class="text-ink-400 hover:text-ink-600">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Resumo --}}
            <div class="bg-ink-50 dark:bg-ink-800 rounded-lg p-4 space-y-1">
                <div class="flex justify-between text-sm">
                    <span class="text-ink-600 dark:text-ink-300">Total da venda</span>
                    <span class="font-semibold text-ink-900 dark:text-ink-50">
                        R$ {{ number_format($this->totalCarrinho, 2, ',', '.') }}
                    </span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-ink-600 dark:text-ink-300">Valor pendente</span>
                    <span class="font-bold {{ $valorPendente > 0 ? 'text-red-600' : 'text-green-600' }}">
                        R$ {{ number_format($valorPendente, 2, ',', '.') }}
                    </span>
                </div>
            </div>

            {{-- Pagamentos já lançados --}}
            @if(count($pagamentos) > 0)
            <div class="space-y-1">
                <p class="text-xs font-semibold text-ink-500 uppercase tracking-wide">Pagamentos lançados</p>
                @foreach($pagamentos as $index => $pag)
                <div class="flex items-center justify-between bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg px-3 py-2">
                    <span class="text-sm text-ink-700 dark:text-ink-200 capitalize">{{ str_replace('_', ' ', $pag['forma']) }}</span>
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-semibold text-green-700 dark:text-green-400">
                            R$ {{ number_format($pag['valor'], 2, ',', '.') }}
                        </span>
                        @if($pag['troco'] > 0)
                        <span class="text-xs text-amber-600">(Troco: R$ {{ number_format($pag['troco'], 2, ',', '.') }})</span>
                        @endif
                        <button wire:click="removerPagamento({{ $index }})" class="text-red-400 hover:text-red-600 text-lg leading-none">×</button>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Formas de pagamento --}}
            <div class="grid grid-cols-4 gap-2">
                @foreach([
                ['dinheiro', 'Dinheiro', 'M12 8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3zm0-6C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z'],
                ['cartao_credito', 'Crédito', 'M20 4H4c-1.11 0-2 .89-2 2v12c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z'],
                ['cartao_debito', 'Débito', 'M20 4H4c-1.11 0-2 .89-2 2v12c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z'],
                ['pix', 'PIX', 'M9.5 6.5v3h-3v-3h3M11 5H5v6h6V5zm-1.5 9.5v3h-3v-3h3M11 13H5v6h6v-6zm6.5-6.5v3h-3v-3h3M19 5h-6v6h6V5zm-6 8h1.5v1.5H13V13zm1.5 1.5H16V16h-1.5v-1.5zM16 13h1.5v1.5H16V13zm-3 3h1.5v1.5H13V16zm1.5 1.5H16V19h-1.5v-1.5zM16 16h1.5v1.5H16V16zm1.5-1.5H19V16h-1.5v-1.5zm0 3H19V19h-1.5v-1.5zM22 7h-2V4h-3V2h5v5zm0 15v-5h-2v3h-3v2h5zM2 22h5v-2H4v-3H2v5zM2 2v5h2V4h3V2H2z'],
                ] as [$valor, $label, $path])
                <button
                    wire:click="$set('formaPagamento', '{{ $valor }}')"
                    class="p-3 border rounded-lg text-center transition-colors text-xs font-medium
                                   {{ $formaPagamento === $valor
                                        ? 'bg-ink-900 text-white border-ink-900 dark:bg-ink-100 dark:text-ink-900'
                                        : 'bg-white dark:bg-ink-800 text-ink-700 dark:text-ink-200 border-gray-200 dark:border-ink-600 hover:bg-ink-50 dark:hover:bg-ink-700' }}">
                    <svg class="size-5 mx-auto mb-1" fill="currentColor" viewBox="0 0 24 24">
                        <path d="{{ $path }}" />
                    </svg>
                    {{ $label }}
                </button>
                @endforeach
            </div>

            {{-- Valor --}}
            <div>
                <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">
                    Valor a pagar
                </label>
                <input
                    type="number"
                    wire:model="valorPagamento"
                    step="0.01"
                    min="0.01"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-lg font-semibold dark:bg-ink-800 dark:text-ink-100 focus:outline-none focus:ring-2 focus:ring-ink-500"
                    placeholder="0,00">
                {{-- Troco (só para dinheiro) --}}
                @if($formaPagamento === 'dinheiro' && $valorPagamento > $valorPendente && $valorPagamento > 0)
                <p class="mt-1 text-sm text-amber-600 font-medium">
                    Troco: R$ {{ number_format($valorPagamento - $valorPendente, 2, ',', '.') }}
                </p>
                @endif
            </div>

            {{-- Botões --}}
            <div class="flex gap-3 pt-1">
                <button
                    wire:click="fecharModalPagamento"
                    class="flex-1 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-sm font-medium text-ink-700 dark:text-ink-200 hover:bg-ink-50 dark:hover:bg-ink-800 transition-colors">
                    Cancelar
                </button>
                <button
                    wire:click="adicionarPagamento"
                    class="flex-2 px-6 py-2 bg-ink-900 dark:bg-ink-100 text-white dark:text-ink-900 rounded-lg text-sm font-medium hover:bg-ink-800 dark:hover:bg-ink-200 transition-colors">
                    {{ $valorPendente <= 0 ? 'Confirmar' : 'Adicionar Pagamento' }}
                </button>
            </div>
        </div>
    </div>
    @endif

   @push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('keydown', function(e) {
            if (['INPUT','TEXTAREA','SELECT'].includes(e.target.tagName)) return;

            if (e.key === 'F2') {
                e.preventDefault();
                document.getElementById('campo-codigo')?.focus();
            }

            if (e.key === 'F5') {
                e.preventDefault();
                Livewire.find(document.querySelector('[wire\\:id]')?.getAttribute('wire:id'))?.call('abrirPagamento');
            }

            if (e.key === 'F6') {
                e.preventDefault();
                if (confirm('Nova venda?')) {
                    const wire = Livewire.find(document.querySelector('[wire\\:id]')?.getAttribute('wire:id'));
                    wire?.set('mesa', '');
                    wire?.set('modoComanda', false);
                    wire?.set('comandaId', null);
                    wire?.call('limparCarrinho');
                }
            }
        });

        document.addEventListener('focar-codigo', () => {
            document.getElementById('campo-codigo')?.focus();
        });
    });
</script>
@endpush
</div>