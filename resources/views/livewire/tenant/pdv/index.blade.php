<div
    x-data="pdvTelaUnica()"
    x-init="init()"
    class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100 dark:from-ink-950 dark:to-ink-950 p-2 sm:p-3">
    {{-- CABEÇALHO COMPACTO --}}
    <div class="mb-2 flex flex-col xl:flex-row justify-between items-start xl:items-center gap-2">
        <div class="flex items-center gap-3">
            <div class="size-10 rounded-xl bg-gradient-to-br from-ink-800 to-ink-900 dark:from-ink-200 dark:to-ink-100 flex items-center justify-center shadow-lg shrink-0">
                <svg class="size-5 text-white dark:text-ink-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.4 5.6A1 1 0 006.6 20h10.8a1 1 0 001-1.4L17 13M9 20a1 1 0 100 2 1 1 0 000-2zm8 0a1 1 0 100 2 1 1 0 000-2z" />
                </svg>
            </div>

            <div>
                <h1 class="text-lg sm:text-xl font-black bg-gradient-to-r from-ink-900 to-ink-600 dark:from-ink-100 dark:to-ink-400 bg-clip-text text-transparent leading-tight">
                    PONTO DE VENDA
                </h1>
                <p class="text-[10px] text-ink-500 dark:text-ink-400">
                    Tela única: produtos, carrinho e pagamento sem modal.
                </p>
            </div>
        </div>

        {{-- Atalhos --}}
        <div class="w-full xl:w-auto overflow-x-auto">
            <div class="flex items-center gap-2 bg-white/90 dark:bg-ink-800/90 backdrop-blur-sm px-3 py-1.5 rounded-full shadow-sm border border-gray-200 dark:border-ink-700 min-w-max">
                <div class="flex items-center gap-1"><kbd class="pdv-kbd">F2</kbd><span class="pdv-shortcut-text">Código</span></div>
                <div class="pdv-separator"></div>
                <div class="flex items-center gap-1"><kbd class="pdv-kbd">F3</kbd><span class="pdv-shortcut-text">Busca</span></div>
                <div class="pdv-separator"></div>
                <div class="flex items-center gap-1"><kbd class="pdv-kbd">F4</kbd><span class="pdv-shortcut-text">Qtd</span></div>
                <div class="pdv-separator"></div>
                <div class="flex items-center gap-1"><kbd class="pdv-kbd">F5</kbd><span class="pdv-shortcut-text">Valor</span></div>
                <div class="pdv-separator"></div>
                <div class="flex items-center gap-1"><kbd class="pdv-kbd">F6</kbd><span class="pdv-shortcut-text">Novo</span></div>
                <div class="pdv-separator"></div>
                <div class="flex items-center gap-1"><kbd class="pdv-kbd">F7</kbd><span class="pdv-shortcut-text">Dinheiro</span></div>
                <div class="pdv-separator"></div>
                <div class="flex items-center gap-1"><kbd class="pdv-kbd">F8</kbd><span class="pdv-shortcut-text">Crédito</span></div>
                <div class="pdv-separator"></div>
                <div class="flex items-center gap-1"><kbd class="pdv-kbd">F9</kbd><span class="pdv-shortcut-text">Débito</span></div>
                <div class="pdv-separator"></div>
                <div class="flex items-center gap-1"><kbd class="pdv-kbd">F10</kbd><span class="pdv-shortcut-text">PIX</span></div>
                <div class="pdv-separator"></div>
                <div class="flex items-center gap-1"><kbd class="pdv-kbd">Ctrl+Enter</kbd><span class="pdv-shortcut-text">Finalizar</span></div>
            </div>
        </div>
    </div>

    {{-- STATUS DO CAIXA COMPACTO --}}
    @if($this->caixaAberto)
    <div class="mb-2 flex flex-wrap items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-emerald-50 to-green-50 dark:from-emerald-900/20 dark:to-green-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl shadow-sm">
        <span class="relative size-2.5 rounded-full bg-emerald-500 block">
            <span class="absolute inset-0 rounded-full bg-emerald-500 animate-ping"></span>
        </span>
        <span class="font-bold text-emerald-700 dark:text-emerald-400 text-xs">💰 CAIXA ABERTO</span>
        <span class="text-emerald-600 dark:text-emerald-500 text-xs hidden sm:inline">•</span>
        <span class="text-xs text-emerald-600 dark:text-emerald-400 font-mono">{{ $this->caixaAberto->aberto_em->format('d/m H:i') }}</span>
        <span class="text-emerald-600 dark:text-emerald-500 text-xs hidden sm:inline">•</span>
        <span class="text-xs text-emerald-600 dark:text-emerald-400">{{ $this->caixaAberto->operador->name ?? 'OPERADOR' }}</span>
        <span class="text-emerald-600 dark:text-emerald-500 text-xs hidden sm:inline">•</span>
        <span class="text-xs text-emerald-600 dark:text-emerald-400">{{ $this->caixaAberto->quantidade_vendas }} vendas</span>
        <span class="text-emerald-600 dark:text-emerald-500 text-xs hidden sm:inline">•</span>
        <span class="text-xs font-black text-emerald-700 dark:text-emerald-400">R$ {{ number_format($this->caixaAberto->total_vendas, 2, ',', '.') }}</span>
    </div>
    @else
    <div class="mb-2 flex items-center justify-between gap-3 px-3 py-1.5 bg-gradient-to-r from-rose-50 to-red-50 dark:from-rose-900/20 dark:to-red-900/20 border border-rose-200 dark:border-rose-800 rounded-xl shadow-sm">
        <div class="flex items-center gap-2">
            <span class="size-2 rounded-full bg-red-500 animate-pulse"></span>
            <span class="font-bold text-red-700 dark:text-red-400 text-xs">⚠️ NENHUM CAIXA ABERTO</span>
            <span class="hidden sm:inline text-xs text-red-600 dark:text-red-500">— Abra o caixa para iniciar as vendas</span>
        </div>
        <a href="{{ route('tenant.caixa') }}"
            class="px-3 py-1 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white rounded-lg text-xs font-bold transition-all shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
            ABRIR CAIXA
        </a>
    </div>
    @endif

    @php
    $totalPago = round(collect($pagamentos)->sum('valor'), 2);
    $pendente = max(0, $this->totalCarrinho - $totalPago);
    @endphp

    {{-- TELA ÚNICA EM 3 COLUNAS: PRODUTOS | CARRINHO | PAGAMENTO --}}
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-2 xl:h-[calc(100vh-118px)] min-h-0">

        {{-- COLUNA 1: PRODUTOS --}}
        <section class="xl:col-span-6 bg-white dark:bg-ink-900 rounded-2xl border border-gray-200 dark:border-ink-700 shadow-lg overflow-hidden flex flex-col min-h-[520px] xl:min-h-0">

            {{-- Busca --}}
            <div class="p-2.5 space-y-2 border-b border-gray-100 dark:border-ink-800 bg-gradient-to-b from-white to-gray-50 dark:from-ink-900 dark:to-ink-800/30 shrink-0">
                <div class="grid grid-cols-12 gap-2">
                    <div class="col-span-12 md:col-span-5 relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="size-4 text-ink-400 group-focus-within:text-primary-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                            </svg>
                        </div>
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="busca"
                            placeholder="Buscar produto..."
                            id="campo-busca"
                            autocomplete="off"
                            class="w-full pl-9 pr-3 py-2 border border-gray-200 dark:border-ink-700 rounded-xl bg-white dark:bg-ink-800 text-sm focus:outline-none focus:ring-2 focus:ring-ink-500 focus:border-transparent transition-all">
                    </div>

                    <div class="col-span-8 md:col-span-5 relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="size-4 text-ink-400 group-focus-within:text-primary-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h1m1 0h1M4 12h1m1 0h1M4 18h1m1 0h1M15 6h1m1 0h1M15 12h1m1 0h1M15 18h1m1 0h1M9 3v18M12 3v18" />
                            </svg>
                        </div>
                        <input
                            type="text"
                            wire:model="codigoProduto"
                            wire:keydown.enter="buscarPorCodigo"
                            placeholder="Código / ID"
                            id="campo-codigo"
                            autocomplete="off"
                            class="w-full pl-9 pr-3 py-2 border border-gray-200 dark:border-ink-700 rounded-xl bg-white dark:bg-ink-800 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-ink-500 focus:border-transparent transition-all">
                    </div>

                    <div class="col-span-4 md:col-span-2 relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="size-4 text-ink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                            </svg>
                        </div>
                        <input
                            type="text"
                            placeholder="1,000"
                            id="campo-quantidade"
                            x-data="{ raw: '' }"
                            x-on:keydown.enter.prevent="
                                let num = raw === '' ? 1 : parseInt(raw) / 1000;
                                $wire.set('quantidadeInput', num).then(() => {
                                    const codigo = document.getElementById('campo-codigo');
                                    if (codigo) {
                                        codigo.dispatchEvent(new KeyboardEvent('keydown', { key: 'Enter', bubbles: true }));
                                        codigo.focus();
                                    }
                                });
                            "
                            x-on:keydown="
                                if (event.key === 'Enter') return;
                                if (event.key === 'Tab') return;

                                if (event.key === 'Backspace') {
                                    raw = raw.slice(0, -1);
                                } else if (event.key >= '0' && event.key <= '9') {
                                    raw = raw + event.key;
                                } else {
                                    return;
                                }

                                event.preventDefault();

                                let num = raw === '' ? 0 : parseInt(raw) / 1000;
                                $el.value = num.toLocaleString('pt-BR', {
                                    minimumFractionDigits: 3,
                                    maximumFractionDigits: 3
                                });
                            "
                            x-on:focus="raw = ''; $el.value = '1,000'; $el.select();"
                            x-on:blur="
                                let num = raw === '' ? 1 : parseInt(raw) / 1000;
                                $wire.set('quantidadeInput', num);
                                raw = '';
                            "
                            class="w-full pl-9 pr-2 py-2 border border-gray-200 dark:border-ink-700 rounded-xl bg-white dark:bg-ink-800 text-sm text-center font-black focus:outline-none focus:ring-2 focus:ring-ink-500 focus:border-transparent transition-all">
                    </div>
                </div>

                {{-- Categorias --}}
                <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-thin">
                    <button wire:click="selecionarCategoria(null)"
                        class="px-3 py-1.5 rounded-xl text-[11px] font-bold whitespace-nowrap transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-ink-500 focus:ring-offset-2
                            {{ !$categoriaSelecionada ? 'bg-gradient-to-r from-ink-800 to-ink-900 text-white shadow-md' : 'bg-gray-100 text-ink-700 hover:bg-gray-200 dark:bg-ink-800 dark:text-ink-300' }}">
                        📦 TODOS
                    </button>

                    @foreach($categorias as $categoria)
                    <button wire:click="selecionarCategoria({{ $categoria->id }})"
                        class="px-3 py-1.5 rounded-xl text-[11px] font-bold whitespace-nowrap transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-ink-500 focus:ring-offset-2
                                {{ $categoriaSelecionada == $categoria->id ? 'bg-gradient-to-r from-ink-800 to-ink-900 text-white shadow-md' : 'bg-gray-100 text-ink-700 hover:bg-gray-200 dark:bg-ink-800 dark:text-ink-300' }}">
                        {{ $categoria->icone ?? '📌' }} {{ $categoria->nome }}
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Grid de Produtos --}}
            <div class="flex-1 min-h-0 overflow-y-auto p-2 scrollbar-thin">
                <div class="grid grid-cols-[repeat(auto-fill,minmax(105px,1fr))] gap-2 content-start">
                    @forelse($produtos as $produto)
                    <button
                        wire:click="adicionarProduto({{ $produto->id }})"
                        wire:key="prod-{{ $produto->id }}"
                        class="group bg-white dark:bg-ink-800 rounded-xl border border-gray-200 dark:border-ink-700 p-2 text-center hover:shadow-xl transition-all duration-200 hover:-translate-y-0.5 active:scale-95 focus:outline-none focus:ring-2 focus:ring-ink-500 focus:ring-offset-2">
                        <div class="w-full h-12 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-ink-700 dark:to-ink-600 rounded-lg flex items-center justify-center mb-1.5 group-hover:scale-105 transition-transform">
                            <span class="text-2xl xl:text-3xl">{{ $produto->icone ?? '📦' }}</span>
                        </div>
                        <p class="font-bold text-[11px] leading-tight line-clamp-2 text-ink-800 dark:text-ink-200 mb-1">{{ $produto->nome }}</p>
                        <p class="text-xs xl:text-sm font-black text-primary-600">R$ {{ number_format($produto->preco_atual, 2, ',', '.') }}</p>
                        @if($produto->codigo)
                        <p class="text-[10px] text-ink-400 font-mono mt-0.5">#{{ $produto->codigo }}</p>
                        @endif
                    </button>
                    @empty
                    <div class="col-span-full text-center py-16">
                        <svg class="size-16 mx-auto text-ink-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7H4a1 1 0 00-1 1v10a1 1 0 001 1h16a1 1 0 001-1V8a1 1 0 00-1-1zM16 3H8l-1 4h10l-1-4z" />
                        </svg>
                        <p class="text-ink-500 font-medium">Nenhum produto encontrado</p>
                        <p class="text-xs text-ink-400 mt-1">Cadastre produtos para começar a vender</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- COLUNA 2: CARRINHO --}}
        <section class="xl:col-span-3 bg-white dark:bg-ink-900 rounded-2xl border border-gray-200 dark:border-ink-700 shadow-lg overflow-hidden flex flex-col min-h-[520px] xl:min-h-0">

            {{-- Mesa / Cliente --}}
            <div class="px-3 py-2.5 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-ink-800 dark:to-ink-800/50 border-b border-gray-200 dark:border-ink-700 shrink-0">
                <div class="flex justify-between items-center mb-2">
                    <div>
                        <span class="font-black text-ink-900 dark:text-ink-50 text-sm">CARRINHO</span>
                        <span class="text-xs font-medium text-ink-500 ml-1">({{ number_format($this->totalItens, 0) }})</span>
                    </div>

                    @if(count($carrinho) > 0)
                    <button wire:click="limparCarrinho" wire:confirm="Limpar carrinho?"
                        class="text-xs font-semibold text-red-500 hover:text-red-700 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 rounded-lg px-2 py-1">
                        LIMPAR
                    </button>
                    @endif
                </div>

                <div class="grid grid-cols-2 xl:grid-cols-1 2xl:grid-cols-2 gap-2">
                    <div>
                        <label class="text-[10px] font-bold text-ink-500 uppercase tracking-wider">Mesa</label>
                        <input
                            type="text"
                            wire:model.live.debounce.500ms="mesa"
                            id="campo-mesa"
                            class="w-full mt-1 px-2.5 py-1.5 text-sm border border-gray-200 dark:border-ink-700 rounded-lg bg-white dark:bg-ink-800 focus:outline-none focus:ring-2 focus:ring-ink-500 transition-all"
                            placeholder="Nº">
                    </div>

                    <div>
                        <label class="text-[10px] font-bold text-ink-500 uppercase tracking-wider">Cliente</label>
                        <select
                            wire:model="clienteId"
                            id="campo-cliente"
                            class="w-full mt-1 px-2.5 py-1.5 text-sm border border-gray-200 dark:border-ink-700 rounded-lg bg-white dark:bg-ink-800 focus:outline-none focus:ring-2 focus:ring-ink-500 transition-all">
                            <option value="">Consumidor</option>
                            @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Itens --}}
            <div class="flex-1 min-h-0 overflow-y-auto divide-y divide-gray-100 dark:divide-ink-700 scrollbar-thin">
                @forelse($carrinho as $chave => $item)
                <div class="px-3 py-2.5 hover:bg-gray-50 dark:hover:bg-ink-800/30 transition-colors" wire:key="item-{{ $chave }}">
                    <div class="flex justify-between items-start gap-2">
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-sm text-ink-900 dark:text-ink-100 truncate">{{ $item['nome'] }}</p>
                            <p class="text-[11px] text-ink-500">{{ $item['preco_formatado'] }}</p>
                            @if(isset($item['observacao']) && $item['observacao'])
                            <p class="text-[10px] text-amber-600 dark:text-amber-400 mt-1 italic line-clamp-1">Obs: {{ $item['observacao'] }}</p>
                            @endif
                        </div>

                        <button wire:click="removerProduto({{ $item['id'] }})"
                            class="text-red-400 hover:text-red-600 transition-colors p-1 focus:outline-none focus:ring-2 focus:ring-red-500 rounded-lg"
                            title="Remover item">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex justify-between items-center mt-2">
                        <div class="flex items-center gap-1.5">
                            <button wire:click="atualizarQuantidade({{ $item['id'] }}, {{ $item['quantidade'] - 1 }})"
                                class="size-7 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-ink-700 dark:hover:bg-ink-600 text-ink-700 dark:text-ink-100 flex items-center justify-center font-bold transition-colors focus:outline-none focus:ring-2 focus:ring-ink-500">−</button>

                            <span class="w-12 text-center font-bold text-xs">
                                {{ number_format($item['quantidade'], $item['quantidade'] == intval($item['quantidade']) ? 0 : 3, ',', '.') }}
                            </span>

                            <button wire:click="atualizarQuantidade({{ $item['id'] }}, {{ $item['quantidade'] + 1 }})"
                                class="size-7 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-ink-700 dark:hover:bg-ink-600 text-ink-700 dark:text-ink-100 flex items-center justify-center font-bold transition-colors focus:outline-none focus:ring-2 focus:ring-ink-500">+</button>
                        </div>

                        <p class="font-black text-xs text-ink-900 dark:text-ink-100">
                            R$ {{ number_format($item['subtotal'], 2, ',', '.') }}
                        </p>
                    </div>
                </div>
                @empty
                <div class="h-full flex flex-col items-center justify-center py-14 text-center">
                    <svg class="size-14 text-ink-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <p class="text-ink-500 font-medium">Carrinho vazio</p>
                    <p class="text-xs text-ink-400 mt-1">Digite o código e pressione Enter</p>
                </div>
                @endforelse
            </div>

            {{-- Total compacto do carrinho --}}
            <div class="shrink-0 border-t border-gray-200 dark:border-ink-700 p-3 bg-gray-50 dark:bg-ink-800/60">
                <div class="flex items-end justify-between gap-2">
                    <div>
                        <p class="text-[10px] uppercase font-black text-ink-400">Total</p>
                        <p class="text-2xl font-black text-primary-600 leading-none">R$ {{ number_format($this->totalCarrinho, 2, ',', '.') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] uppercase font-black text-ink-400">Falta</p>
                        <p class="text-sm font-black {{ $pendente > 0 ? 'text-red-600' : 'text-green-600' }}">R$ {{ number_format($pendente, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- COLUNA 3: PAGAMENTO --}}
        <section class="xl:col-span-3 bg-white dark:bg-ink-900 rounded-2xl border border-gray-200 dark:border-ink-700 shadow-lg overflow-hidden flex flex-col min-h-[520px] xl:min-h-0">

            <div class="px-3 py-2.5 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-ink-800 dark:to-ink-800/50 border-b border-gray-200 dark:border-ink-700 shrink-0">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-black text-sm text-ink-900 dark:text-ink-50">PAGAMENTO</h2>
                        <p class="text-[10px] text-ink-500">Sem modal, direto na tela</p>
                    </div>
                    <span class="text-xs font-black px-2 py-1 rounded-full {{ $pendente > 0 ? 'bg-red-50 text-red-600 dark:bg-red-900/20' : 'bg-green-50 text-green-600 dark:bg-green-900/20' }}">
                        {{ $pendente > 0 ? 'PENDENTE' : 'OK' }}
                    </span>
                </div>
            </div>

            <div class="flex-1 min-h-0 overflow-y-auto scrollbar-thin p-3 space-y-3">
                {{-- Totais --}}
                <div class="grid grid-cols-1 2xl:grid-cols-3 gap-2">
                    <div class="rounded-xl bg-gray-50 dark:bg-ink-800 border border-gray-200 dark:border-ink-700 px-3 py-2">
                        <p class="text-[10px] uppercase font-black text-ink-400">Total</p>
                        <p class="text-base font-black text-primary-600 leading-tight">R$ {{ number_format($this->totalCarrinho, 2, ',', '.') }}</p>
                    </div>

                    <div class="rounded-xl bg-gray-50 dark:bg-ink-800 border border-gray-200 dark:border-ink-700 px-3 py-2">
                        <p class="text-[10px] uppercase font-black text-ink-400">Pago</p>
                        <p class="text-base font-black text-green-600 leading-tight">R$ {{ number_format($totalPago, 2, ',', '.') }}</p>
                    </div>

                    <div class="rounded-xl bg-gray-50 dark:bg-ink-800 border border-gray-200 dark:border-ink-700 px-3 py-2">
                        <p class="text-[10px] uppercase font-black text-ink-400">Falta</p>
                        <p class="text-base font-black leading-tight {{ $pendente > 0 ? 'text-red-600' : 'text-green-600' }}">R$ {{ number_format($pendente, 2, ',', '.') }}</p>
                    </div>
                </div>

                {{-- Formas --}}
                <div class="grid grid-cols-2 gap-2">
                    <button type="button"
                        @click="selecionarPagamento('dinheiro')"
                        wire:click="$set('formaPagamento', 'dinheiro')"
                        class="pdv-payment-btn {{ $formaPagamento === 'dinheiro' ? 'pdv-payment-active' : 'pdv-payment-default' }}">
                        <span class="text-xl">💰</span>
                        <span class="text-[10px] font-black uppercase">F7 Dinheiro</span>
                    </button>

                    <button type="button"
                        @click="selecionarPagamento('cartao_credito')"
                        wire:click="$set('formaPagamento', 'cartao_credito')"
                        class="pdv-payment-btn {{ $formaPagamento === 'cartao_credito' ? 'pdv-payment-active' : 'pdv-payment-default' }}">
                        <span class="text-xl">💳</span>
                        <span class="text-[10px] font-black uppercase">F8 Crédito</span>
                    </button>

                    <button type="button"
                        @click="selecionarPagamento('cartao_debito')"
                        wire:click="$set('formaPagamento', 'cartao_debito')"
                        class="pdv-payment-btn {{ $formaPagamento === 'cartao_debito' ? 'pdv-payment-active' : 'pdv-payment-default' }}">
                        <span class="text-xl">💳</span>
                        <span class="text-[10px] font-black uppercase">F9 Débito</span>
                    </button>

                    <button type="button"
                        @click="selecionarPagamento('pix')"
                        wire:click="$set('formaPagamento', 'pix')"
                        class="pdv-payment-btn {{ $formaPagamento === 'pix' ? 'pdv-payment-active' : 'pdv-payment-default' }}">
                        <span class="text-xl">📱</span>
                        <span class="text-[10px] font-black uppercase">F10 PIX</span>
                    </button>
                </div>

                {{-- Valor --}}
                <div>
                    <label class="text-[10px] font-black uppercase text-ink-500">Valor recebido</label>
                   <input
                        type="text"
                        inputmode="decimal"
                        id="valor-pagamento"
                        x-ref="valorPagamento"
                        x-model="valorPagamentoLocal"
                        x-init="valorPagamentoLocal = @js(number_format($valorPagamento > 0 ? $valorPagamento : $pendente, 2, ',', '.'))"
                        @keydown.enter.prevent="adicionarPagamentoPeloTeclado()"
                        class="w-full mt-1 px-3 py-3 border-2 border-gray-200 dark:border-ink-700 rounded-xl text-center font-black text-2xl focus:border-primary-500 outline-none transition-all bg-white dark:bg-ink-900"
                        placeholder="0,00">
                </div>

                <button type="button"
                    @click="adicionarPagamentoPeloTeclado()"
                    @if(empty($carrinho)) disabled @endif
                    class="w-full py-3 bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-xl font-black text-xs uppercase tracking-wider hover:from-amber-600 hover:to-orange-600 disabled:opacity-40 disabled:cursor-not-allowed shadow-md transition-all focus:outline-none focus:ring-2 focus:ring-amber-500">
                    ➕ Adicionar Pagamento / Enter
                </button>

                {{-- Pagamentos --}}
                <div class="space-y-1.5">
                    <p class="text-[10px] uppercase font-black text-ink-400">Pagamentos adicionados</p>

                    <div class="space-y-1.5">
                        @forelse($pagamentos as $index => $pag)
                        <div class="flex items-center justify-between bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg px-2 py-1.5">
                            <div class="flex items-center gap-2 min-w-0">
                                <span>{{ $pag['forma'] === 'dinheiro' ? '💰' : ($pag['forma'] === 'pix' ? '📱' : '💳') }}</span>
                                <span class="text-xs font-bold capitalize truncate">{{ str_replace('_', ' ', $pag['forma']) }}</span>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="text-xs font-black text-green-700 dark:text-green-400">R$ {{ number_format($pag['valor'], 2, ',', '.') }}</span>
                                <button wire:click="removerPagamento({{ $index }})"
                                    class="text-red-500 hover:text-red-700 text-lg leading-none focus:outline-none focus:ring-2 focus:ring-red-500 rounded px-1"
                                    title="Remover pagamento">×</button>
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-xs text-ink-400 py-3 border border-dashed border-gray-200 dark:border-ink-700 rounded-lg">
                            Nenhum pagamento adicionado
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Ações finais fixas --}}
            <div class="shrink-0 border-t border-gray-200 dark:border-ink-700 p-3 bg-gray-50 dark:bg-ink-800/60 space-y-2">
                @if($modoComanda && $mesa)
                <button wire:click="salvarComanda"
                    @if(empty($carrinho)) disabled @endif
                    class="w-full py-2.5 border-2 border-amber-500 text-amber-600 dark:text-amber-400 rounded-xl font-black text-xs uppercase tracking-wider hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-all disabled:opacity-40 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    💾 Salvar Mesa ({{ $mesa }})
                </button>
                @endif

                <button wire:click="finalizarVenda"
                    data-finalizar-venda="true"
                    @if(empty($carrinho) || $pendente> 0) disabled @endif
                    class="w-full py-3 bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white rounded-xl font-black text-xs uppercase tracking-wider shadow-lg transition-all active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    ✅ Finalizar Venda / Ctrl+Enter
                </button>

                @if($pendente > 0)
                <p class="text-center text-[11px] text-amber-600 font-medium">
                    Falta R$ {{ number_format($pendente, 2, ',', '.') }} para finalizar.
                </p>
                @else
                @if(!empty($carrinho))
                <p class="text-center text-[11px] text-green-600 font-medium">
                    Pagamento completo. Pode finalizar.
                </p>
                @endif
                @endif
            </div>
        </section>
    </div>

    {{-- MODAL PARA NF NA FINALIZAÇÃO --}}
    @if($mostrarModalNF)
    @php
    $numerosDocumentoNF = preg_replace('/[^0-9]/', '', $cpfCnpjNF ?? '');
    $ehCnpjNF = strlen($numerosDocumentoNF) === 14;
    $ehCpfNF = strlen($numerosDocumentoNF) === 11;
    @endphp

    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-ink-900 rounded-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto p-6 shadow-2xl transform transition-all animate-in zoom-in-95 duration-200">

            <div class="flex items-center gap-3 mb-4">
                <div class="size-12 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center">
                    <svg class="size-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>

                <div>
                    <h2 class="text-xl font-bold text-ink-900 dark:text-ink-50">
                        Emitir Nota Fiscal
                    </h2>
                    <p class="text-xs text-ink-500 dark:text-ink-400">
                        CPF = NFC-e | CNPJ = NF-e
                    </p>
                </div>
            </div>

            <p class="text-sm text-ink-500 dark:text-ink-400 mb-4">
                Preencha os dados do cliente para emitir a nota fiscal.
            </p>

            <div class="space-y-4">

                {{-- CPF / CNPJ --}}
                <div>
                    <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">
                        CPF / CNPJ *
                    </label>

                    <input type="text"
                        wire:model.live.debounce.300ms="cpfCnpjNF"
                        x-data="{
                            formatarDocumento(value) {
                                let numeros = value.replace(/\D/g, '');

                                if (numeros.length <= 11) {
                                    numeros = numeros.substring(0, 11);
                                    if (numeros.length > 3) numeros = numeros.replace(/(\d{3})(\d)/, '$1.$2');
                                    if (numeros.length > 6) numeros = numeros.replace(/(\d{3})(\d)/, '$1.$2');
                                    if (numeros.length > 9) numeros = numeros.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                                } else {
                                    numeros = numeros.substring(0, 14);
                                    numeros = numeros.replace(/^(\d{2})(\d)/, '$1.$2');
                                    numeros = numeros.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
                                    numeros = numeros.replace(/\.(\d{3})(\d)/, '.$1/$2');
                                    numeros = numeros.replace(/(\d{4})(\d)/, '$1-$2');
                                }

                                return numeros;
                            }
                        }"
                        x-on:input="
                            $event.target.value = formatarDocumento($event.target.value);
                            $wire.set('cpfCnpjNF', $event.target.value);
                        "
                        autocomplete="off"
                        class="w-full px-4 py-2.5 border border-gray-200 dark:border-ink-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-ink-500 transition-all"
                        placeholder="000.000.000-00 ou 00.000.000/0000-00">

                    <div class="mt-1">
                        @if($ehCpfNF)
                        <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">
                            CPF → NFC-e
                        </span>
                        @elseif($ehCnpjNF)
                        <span class="text-xs px-2 py-0.5 rounded-full bg-purple-100 text-purple-700">
                            CNPJ → NF-e
                        </span>
                        @endif
                    </div>

                    @error('cpfCnpjNF')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nome --}}
                <div>
                    <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">
                        Nome / Razão Social *
                    </label>

                    <input type="text"
                        wire:model="nomeClienteNF"
                        autocomplete="off"
                        class="w-full px-4 py-2.5 border border-gray-200 dark:border-ink-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-ink-500 transition-all"
                        placeholder="Nome completo ou razão social">

                    @error('nomeClienteNF')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- CAMPOS EXTRAS PARA NF-E --}}
                @if($ehCnpjNF)
                <div class="border-t border-gray-200 dark:border-ink-700 pt-4 mt-4">
                    <div class="mb-3">
                        <h3 class="text-sm font-semibold text-ink-900 dark:text-ink-50">
                            Dados obrigatórios para NF-e
                        </h3>
                        <p class="text-xs text-ink-500 dark:text-ink-400">
                            Para CNPJ, informe endereço completo e telefone válido.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        {{-- Inscrição Estadual --}}
                        <div>
                            <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">
                                Inscrição Estadual
                            </label>

                            <input type="text"
                                wire:model="inscricaoEstadualNF"
                                autocomplete="off"
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-ink-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-ink-500 transition-all"
                                placeholder="Ex.: 6010187484 ou ISENTO">

                            @error('inscricaoEstadualNF')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Telefone --}}
                        <div>
                            <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">
                                Telefone *
                            </label>

                            <input type="text"
                                wire:model="telefoneNF"
                                x-data="{
                                        formatarTelefone(value) {
                                            let numeros = value.replace(/\D/g, '').substring(0, 11);

                                            if (numeros.length <= 10) {
                                                numeros = numeros.replace(/^(\d{2})(\d)/, '($1) $2');
                                                numeros = numeros.replace(/(\d{4})(\d)/, '$1-$2');
                                            } else {
                                                numeros = numeros.replace(/^(\d{2})(\d)/, '($1) $2');
                                                numeros = numeros.replace(/(\d{5})(\d)/, '$1-$2');
                                            }

                                            return numeros;
                                        }
                                    }"
                                x-on:input="
                                        $event.target.value = formatarTelefone($event.target.value);
                                        $wire.set('telefoneNF', $event.target.value);
                                    "
                                autocomplete="off"
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-ink-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-ink-500 transition-all"
                                placeholder="(43) 99999-9999">

                            @error('telefoneNF')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- CEP --}}
                        <div>
                            <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">
                                CEP *
                            </label>

                            <input type="text"
                                wire:model="cepNF"
                                x-data="{
                                        formatarCep(value) {
                                            let numeros = value.replace(/\D/g, '').substring(0, 8);
                                            if (numeros.length > 5) {
                                                numeros = numeros.replace(/^(\d{5})(\d)/, '$1-$2');
                                            }
                                            return numeros;
                                        }
                                    }"
                                x-on:input="
                                        $event.target.value = formatarCep($event.target.value);
                                        $wire.set('cepNF', $event.target.value);
                                    "
                                autocomplete="off"
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-ink-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-ink-500 transition-all"
                                placeholder="86025-400">

                            @error('cepNF')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Cidade --}}
                        <div>
                            <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">
                                Município *
                            </label>

                            <input type="text"
                                wire:model="cidadeNF"
                                autocomplete="off"
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-ink-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-ink-500 transition-all"
                                placeholder="Ex.: Londrina">

                            @error('cidadeNF')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- UF --}}
                        <div>
                            <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">
                                UF *
                            </label>

                            <select wire:model="ufNF"
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-ink-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-ink-500 transition-all">
                                <option value="">Selecione</option>
                                <option value="AC">AC</option>
                                <option value="AL">AL</option>
                                <option value="AP">AP</option>
                                <option value="AM">AM</option>
                                <option value="BA">BA</option>
                                <option value="CE">CE</option>
                                <option value="DF">DF</option>
                                <option value="ES">ES</option>
                                <option value="GO">GO</option>
                                <option value="MA">MA</option>
                                <option value="MT">MT</option>
                                <option value="MS">MS</option>
                                <option value="MG">MG</option>
                                <option value="PA">PA</option>
                                <option value="PB">PB</option>
                                <option value="PR">PR</option>
                                <option value="PE">PE</option>
                                <option value="PI">PI</option>
                                <option value="RJ">RJ</option>
                                <option value="RN">RN</option>
                                <option value="RS">RS</option>
                                <option value="RO">RO</option>
                                <option value="RR">RR</option>
                                <option value="SC">SC</option>
                                <option value="SP">SP</option>
                                <option value="SE">SE</option>
                                <option value="TO">TO</option>
                            </select>

                            @error('ufNF')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Bairro --}}
                        <div>
                            <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">
                                Bairro *
                            </label>

                            <input type="text"
                                wire:model="bairroNF"
                                autocomplete="off"
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-ink-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-ink-500 transition-all"
                                placeholder="Centro">

                            @error('bairroNF')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Logradouro --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">
                                Logradouro *
                            </label>

                            <input type="text"
                                wire:model="enderecoNF"
                                autocomplete="off"
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-ink-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-ink-500 transition-all"
                                placeholder="Rua, avenida, travessa...">

                            @error('enderecoNF')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Número --}}
                        <div>
                            <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">
                                Número *
                            </label>

                            <input type="text"
                                wire:model="numeroNF"
                                autocomplete="off"
                                class="w-full px-4 py-2.5 border border-gray-200 dark:border-ink-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-ink-500 transition-all"
                                placeholder="1007">

                            @error('numeroNF')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>
                @endif
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button"
                    wire:click="finalizarSemNF"
                    class="px-5 py-2.5 border border-gray-300 dark:border-ink-600 rounded-xl text-ink-700 dark:text-ink-300 hover:bg-gray-50 dark:hover:bg-ink-800 transition-all focus:outline-none focus:ring-2 focus:ring-ink-500">
                    Não emitir
                </button>

                <button type="button"
                    wire:click="emitirNotaDaVenda"
                    wire:loading.attr="disabled"
                    wire:target="emitirNotaDaVenda"
                    class="px-5 py-2.5 bg-gradient-to-r from-ink-800 to-ink-900 text-white rounded-xl font-medium hover:shadow-lg transition-all focus:outline-none focus:ring-2 focus:ring-ink-500 disabled:opacity-50">
                    <span wire:loading.remove wire:target="emitirNotaDaVenda">Emitir Nota</span>
                    <span wire:loading wire:target="emitirNotaDaVenda">Emitindo...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
    @push('scripts')
    <style>
        .pdv-kbd {
            padding: 0.18rem 0.42rem;
            font-size: 0.68rem;
            line-height: 1;
            font-weight: 800;
            border-radius: 0.4rem;
            background: #e5e7eb;
            color: #111827;
            box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .08);
        }

        .dark .pdv-kbd {
            background: #27272a;
            color: #f4f4f5;
        }

        .pdv-shortcut-text {
            font-size: 0.68rem;
            color: #6b7280;
            white-space: nowrap;
        }

        .pdv-separator {
            width: 1px;
            height: 0.9rem;
            background: #d1d5db;
        }

        .dark .pdv-separator {
            background: #3f3f46;
        }

        .pdv-payment-btn {
            display: flex;
            min-height: 4.1rem;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.25rem;
            border-radius: 0.75rem;
            border-width: 2px;
            padding: 0.45rem;
            transition: all .15s ease;
        }

        .pdv-payment-default {
            border-color: #e5e7eb;
            background: #fff;
            color: #374151;
        }

        .pdv-payment-default:hover {
            border-color: #a5b4fc;
            background: #f9fafb;
        }

        .dark .pdv-payment-default {
            border-color: #3f3f46;
            background: #18181b;
            color: #d4d4d8;
        }

        .pdv-payment-active {
            border-color: rgb(79 70 229);
            background: rgb(238 242 255);
            color: rgb(79 70 229);
            box-shadow: 0 8px 18px rgba(79, 70, 229, .15);
        }

        .dark .pdv-payment-active {
            background: rgba(79, 70, 229, .16);
            color: #a5b4fc;
        }

        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .scrollbar-thin::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }

        .dark .scrollbar-thin::-webkit-scrollbar-track {
            background: #1a1a1a;
        }

        .dark .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #4a4a4a;
        }

        @keyframes zoom-in {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .animate-in {
            animation: zoom-in 0.2s ease-out;
        }

        @media (max-height: 760px) and (min-width: 1280px) {
            .pdv-payment-btn {
                min-height: 3.4rem;
                padding: 0.3rem;
            }
        }
    </style>

    <script>
        function pdvTelaUnica() {
        return {
            valorPagamentoLocal: '',

            init() {
                this.$nextTick(() => {
                    this.focarCampo('campo-codigo');
                });

                if (!window.__pdvTelaUnicaAtalhosRegistrados) {
                    window.__pdvTelaUnicaAtalhosRegistrados = true;
                    document.addEventListener('keydown', (e) => this.handleKeydown(e));
                }

                window.addEventListener('focar-codigo', () => {
                    this.focarCampo('campo-codigo');
                });
                window.addEventListener('pdv-resetar-quantidade', () => {
                    const campoQuantidade = document.getElementById('campo-quantidade');

                    if (campoQuantidade) {
                        campoQuantidade.value = '1,000';
                    }
                });
                window.addEventListener('pdv-atualizar-valor-pagamento', (event) => {
                    const detalhe = event.detail;

                    let valorFormatado = '0,00';

                    if (Array.isArray(detalhe) && detalhe[0]?.valor_formatado) {
                        valorFormatado = detalhe[0].valor_formatado;
                    } else if (detalhe?.valor_formatado) {
                        valorFormatado = detalhe.valor_formatado;
                    }

                    this.valorPagamentoLocal = valorFormatado;

                    const input = document.getElementById('valor-pagamento');

                    if (input) {
                        input.value = valorFormatado;
                    }
                });

                document.addEventListener('focar-codigo', () => {
                    this.focarCampo('campo-codigo');
                });
            },

            focarCampo(id) {
                setTimeout(() => {
                    const campo = document.getElementById(id);

                    if (campo) {
                        campo.focus();

                        if (typeof campo.select === 'function') {
                            campo.select();
                        }
                    }
                }, 80);
            },

            selecionarPagamento(forma) {
                this.$wire.set('formaPagamento', forma);
                this.focarCampo('campo-codigo');
            },

            parseValorPagamento(valor) {
                let texto = String(valor ?? '').trim();

                if (!texto) {
                    return 0;
                }

                texto = texto.replace(/[^0-9,.-]/g, '');

                if (texto.includes(',')) {
                    texto = texto.replace(/\./g, '').replace(',', '.');
                }

                return Number(texto);
            },

            formatarMoeda(valor) {
                return Number(valor || 0).toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            },

            adicionarPagamentoPeloTeclado() {
                const input = document.getElementById('valor-pagamento');
                const valor = this.parseValorPagamento(this.valorPagamentoLocal || input?.value);

                if (!valor || valor <= 0) {
                    this.focarCampo('valor-pagamento');
                    return;
                }

                this.$wire.set('valorPagamento', valor)
                    .then(() => this.$wire.call('adicionarPagamento'))
                    .then(() => {
                        const restante = Number(this.$wire.valorPendente || 0);

                        this.valorPagamentoLocal = this.formatarMoeda(restante);

                        if (input) {
                            input.value = this.valorPagamentoLocal;
                        }

                        this.focarCampo('campo-codigo');
                    });
            },

            finalizarVendaPeloTeclado() {
                const botao = document.querySelector('[data-finalizar-venda="true"]');

                if (botao && !botao.disabled) {
                    botao.click();
                }
            },

            handleKeydown(e) {
                const target = e.target;
                const tag = target.tagName;
                const isInput = tag === 'INPUT' && target.type !== 'hidden';
                const isSelect = tag === 'SELECT';
                const isTextarea = tag === 'TEXTAREA';

                if (e.ctrlKey && e.key === 'Enter') {
                    e.preventDefault();
                    this.finalizarVendaPeloTeclado();
                    return;
                }

                if (e.key === 'F2') {
                    e.preventDefault();
                    this.focarCampo('campo-codigo');
                    return;
                }

                if (e.key === 'F3') {
                    e.preventDefault();
                    this.focarCampo('campo-busca');
                    return;
                }

                if (e.key === 'F4') {
                    e.preventDefault();
                    this.focarCampo('campo-quantidade');
                    return;
                }

                if (e.key === 'F5') {
                    e.preventDefault();
                    this.focarCampo('valor-pagamento');
                    return;
                }

                if (e.key === 'F6') {
                    e.preventDefault();

                    if (confirm('Nova venda?')) {
                        this.$wire.set('mesa', '');
                        this.$wire.set('modoComanda', false);
                        this.$wire.set('comandaId', null);
                        this.$wire.call('limparCarrinho')
                            .then(() => {
                                this.valorPagamentoLocal = '0,00';
                                this.focarCampo('campo-codigo');
                            });
                    }

                    return;
                }

                if (e.key === 'F7') {
                    e.preventDefault();
                    this.selecionarPagamento('dinheiro');
                    return;
                }

                if (e.key === 'F8') {
                    e.preventDefault();
                    this.selecionarPagamento('cartao_credito');
                    return;
                }

                if (e.key === 'F9') {
                    e.preventDefault();
                    this.selecionarPagamento('cartao_debito');
                    return;
                }

                if (e.key === 'F10') {
                    e.preventDefault();
                    this.selecionarPagamento('pix');
                    return;
                }

                if (e.key === 'Escape') {
                    e.preventDefault();
                    this.focarCampo('campo-codigo');
                    return;
                }

                if (isInput || isSelect || isTextarea) {
                    return;
                }
            }
        }
    }
    </script>
    @endpush
</div>