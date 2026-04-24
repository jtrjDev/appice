<div x-data="{ 
        showPayment: false,
        init() {
            // Forçar foco no campo código ao carregar
            this.$nextTick(() => {
                const campoCodigo = document.getElementById('campo-codigo');
                if (campoCodigo) {
                    campoCodigo.focus();
                }
            });
            
            // Registrar atalho F8 globalmente
            document.addEventListener('keydown', this.handleKeydown.bind(this));
        },
        handleKeydown(e) {
            // Ignora se está digitando em input/textarea/select
            const target = e.target;
            const isInput = target.tagName === 'INPUT' && target.type !== 'hidden';
            const isSelect = target.tagName === 'SELECT';
            const isTextarea = target.tagName === 'TEXTAREA';
            
            if (isInput || isSelect || isTextarea) {
                // Permite F2, F5, F6, F8 mesmo dentro de inputs
                if (!['F2', 'F5', 'F6', 'F8'].includes(e.key)) return;
            }
            
            if (e.key === 'F2') {
                e.preventDefault();
                const campoCodigo = document.getElementById('campo-codigo');
                if (campoCodigo) campoCodigo.focus();
            }
            
            if (e.key === 'F5') {
                e.preventDefault();
                const component = document.querySelector('[wire\\:id]');
                if (!component) return;
                const wireId = component.getAttribute('wire:id');
                const wire = Livewire.find(wireId);
                if (wire) wire.call('acaoF5');
            }
            
            if (e.key === 'F6') {
                e.preventDefault();
                if (confirm('Nova venda?')) {
                    const component = document.querySelector('[wire\\:id]');
                    if (!component) return;
                    const wireId = component.getAttribute('wire:id');
                    const wire = Livewire.find(wireId);
                    if (wire) {
                        wire.set('mesa', '');
                        wire.set('modoComanda', false);
                        wire.set('comandaId', null);
                        wire.call('limparCarrinho');
                    }
                }
            }
            
            if (e.key === 'F8') {
                e.preventDefault();
                this.showPayment = true;
            }
        }
    }"
    class="min-h-screen lg:h-screen lg:overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100 dark:from-ink-900 dark:to-ink-950 p-2 sm:p-3">

    {{-- Cabeçalho --}}
    <div class="mb-3 flex justify-between items-center">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-ink-900 to-ink-600 dark:from-ink-100 dark:to-ink-400 bg-clip-text text-transparent">
                🍦 Ponto de Venda
            </h1>
            <p class="text-xs text-ink-500 mt-0.5">Sistema rápido e intuitivo</p>
        </div>

        <div class="hidden sm:block text-right">
            <div class="flex items-center gap-2 text-xs text-ink-500 bg-ink-100 dark:bg-ink-800 px-3 py-1.5 rounded-full">
                <span class="font-mono">F2</span> <span>🔍 Buscar</span>
                <span class="w-px h-3 bg-ink-300"></span>
                <span class="font-mono">F5</span> <span>✅ Finalizar</span>
                <span class="w-px h-3 bg-ink-300"></span>
                <span class="font-mono">F6</span> <span>🔄 Novo</span>
                <span class="w-px h-3 bg-ink-300"></span>
                <span class="font-mono">F8</span> <span>💳 Pagar</span>
            </div>
        </div>
    </div>

    {{-- Status do Caixa --}}
    @if($this->caixaAberto)
        <div class="mb-3 flex flex-wrap items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800 rounded-xl shadow-sm">
            <div class="relative">
                <span class="absolute inset-0 flex items-center justify-center">
                    <span class="size-2 rounded-full bg-green-500 animate-ping"></span>
                </span>
                <span class="relative size-2 rounded-full bg-green-500 block"></span>
            </div>

            <span class="font-semibold text-green-700 dark:text-green-400 text-sm">💰 Caixa aberto</span>
            <span class="text-green-600 hidden sm:inline">•</span>
            <span class="text-xs sm:text-sm text-green-600 dark:text-green-400">
                {{ $this->caixaAberto->aberto_em->format('d/m H:i') }}
            </span>
            <span class="text-green-600 hidden sm:inline">•</span>
            <span class="text-xs sm:text-sm text-green-600 dark:text-green-400">
                {{ $this->caixaAberto->operador->name ?? 'N/A' }}
            </span>
            <span class="text-green-600 hidden sm:inline">•</span>
            <span class="text-xs sm:text-sm text-green-600 dark:text-green-400">
                {{ $this->caixaAberto->quantidade_vendas }} vendas
            </span>
            <span class="text-green-600 hidden sm:inline">•</span>
            <span class="text-xs sm:text-sm font-bold text-green-700 dark:text-green-400">
                R$ {{ number_format($this->caixaAberto->total_vendas, 2, ',', '.') }}
            </span>
        </div>
    @else
        <div class="mb-3 flex items-center justify-between gap-3 px-3 sm:px-4 py-2 bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 border border-red-200 dark:border-red-800 rounded-xl shadow-sm">
            <div class="flex items-center gap-3">
                <span class="size-2 rounded-full bg-red-500 animate-pulse"></span>
                <div>
                    <span class="font-semibold text-red-700 dark:text-red-400 text-sm">⚠️ Nenhum caixa aberto</span>
                    <span class="hidden sm:inline text-xs sm:text-sm text-red-600 dark:text-red-500">
                        — Abra o caixa para iniciar as vendas
                    </span>
                </div>
            </div>

            <a href="{{ route('tenant.caixa') }}"
                class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-medium transition-all duration-200 shadow-md hover:shadow-lg">
                Abrir Caixa
            </a>
        </div>
    @endif

    {{-- Layout principal compacto para notebook --}}
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-3 lg:h-[calc(100vh-135px)] min-h-0">

        {{-- COLUNA ESQUERDA — Produtos --}}
        <div class="xl:col-span-7 bg-white dark:bg-ink-900 rounded-xl border border-gray-200 dark:border-ink-700 shadow-sm overflow-hidden flex flex-col min-h-0">

            <div class="p-3 space-y-2 shrink-0">

                {{-- Barra de busca --}}
                <div class="grid grid-cols-12 gap-2">

                    <div class="col-span-12 sm:col-span-6 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="size-4 text-ink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                            </svg>
                        </div>

                        <input type="text"
                            wire:model.live.debounce.300ms="busca"
                            placeholder="Buscar produto..."
                            class="w-full pl-9 pr-3 py-2 border border-gray-200 dark:border-ink-700 rounded-lg bg-white dark:bg-ink-800 text-sm focus:outline-none focus:ring-2 focus:ring-ink-500 focus:border-transparent transition-all">
                    </div>

                    <div class="col-span-8 sm:col-span-4 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="size-4 text-ink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h1m1 0h1M4 12h1m1 0h1M4 18h1m1 0h1M15 6h1m1 0h1M15 12h1m1 0h1M15 18h1m1 0h1M9 3v18M12 3v18"/>
                            </svg>
                        </div>

                        <input type="text"
                            wire:model="codigoProduto"
                            wire:keydown.enter="buscarPorCodigo"
                            placeholder="Código / ID"
                            id="campo-codigo"
                            class="w-full pl-9 pr-3 py-2 border border-gray-200 dark:border-ink-700 rounded-lg bg-white dark:bg-ink-800 text-sm focus:outline-none focus:ring-2 focus:ring-ink-500 focus:border-transparent transition-all">
                    </div>

                    <div class="col-span-4 sm:col-span-2 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="size-4 text-ink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                            </svg>
                        </div>

                        <input type="text"
                            placeholder="1,000"
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

                                $el.value = num.toLocaleString('pt-BR', {
                                    minimumFractionDigits: 3,
                                    maximumFractionDigits: 3
                                });
                            "
                            x-on:focus="raw = ''; $el.value = '1,000'"
                            x-on:blur="
                                let num = raw === '' ? 1 : parseInt(raw) / 1000;
                                $wire.set('quantidadeInput', num);
                                raw = '';
                            "
                            class="w-full pl-9 pr-2 py-2 border border-gray-200 dark:border-ink-700 rounded-lg bg-white dark:bg-ink-800 text-sm focus:outline-none focus:ring-2 focus:ring-ink-500 focus:border-transparent transition-all">
                    </div>
                </div>

                {{-- Categorias --}}
                <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-thin">
                    <button wire:click="selecionarCategoria(null)"
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all duration-200
                            {{ !$categoriaSelecionada
                                ? 'bg-ink-900 text-white shadow-md dark:bg-ink-100 dark:text-ink-900'
                                : 'bg-gray-100 text-ink-700 hover:bg-gray-200 dark:bg-ink-800 dark:text-ink-300 dark:hover:bg-ink-700' }}">
                        📦 TODOS
                    </button>

                    @foreach($categorias as $categoria)
                        <button wire:click="selecionarCategoria({{ $categoria->id }})"
                            class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all duration-200
                                {{ $categoriaSelecionada == $categoria->id
                                    ? 'bg-ink-900 text-white shadow-md dark:bg-ink-100 dark:text-ink-900'
                                    : 'bg-gray-100 text-ink-700 hover:bg-gray-200 dark:bg-ink-800 dark:text-ink-300 dark:hover:bg-ink-700' }}">
                            {{ $categoria->icone ?? '📌' }} {{ $categoria->nome }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Grid de Produtos --}}
            <div class="flex-1 min-h-0 overflow-y-auto p-3 pt-0 scrollbar-thin">
                <div class="grid grid-cols-[repeat(auto-fill,minmax(105px,1fr))] gap-2 content-start">

                    @forelse($produtos as $produto)
                        <button wire:click="adicionarProduto({{ $produto->id }})"
                            wire:key="prod-{{ $produto->id }}"
                            class="pdv-card-produto group bg-white dark:bg-ink-900 rounded-lg border border-gray-200 dark:border-ink-700 p-2 text-left hover:shadow-lg transition-all duration-150 active:scale-95">

                            <div class="flex flex-col items-center gap-1">
                                <div class="pdv-icone w-full h-12 bg-gradient-to-br from-ink-50 to-gray-100 dark:from-ink-800 dark:to-ink-700 rounded-md flex items-center justify-center">
                                    <span class="text-2xl">{{ $produto->icone ?? '📦' }}</span>
                                </div>

                                <div class="text-center w-full">
                                    <p class="font-semibold text-[11px] leading-tight line-clamp-2 text-ink-800 dark:text-ink-100">
                                        {{ $produto->nome }}
                                    </p>

                                    <p class="text-xs font-bold text-primary-600 mt-1">
                                        R$ {{ number_format($produto->preco_atual, 2, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        </button>
                    @empty
                        <div class="col-span-full text-center py-12">
                            <svg class="size-12 mx-auto text-ink-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7H4a1 1 0 00-1 1v10a1 1 0 001 1h16a1 1 0 001-1V8a1 1 0 00-1-1zM16 3H8l-1 4h10l-1-4z"/>
                            </svg>
                            <p class="text-ink-400">Nenhum produto encontrado</p>
                        </div>
                    @endforelse

                </div>
            </div>
        </div>

        {{-- COLUNA DIREITA — Carrinho (Totalmente visível) --}}
        <div class="xl:col-span-5 bg-white dark:bg-ink-900 rounded-xl border border-gray-200 dark:border-ink-700 shadow-sm overflow-hidden flex flex-col min-h-0">

            {{-- Cabeçalho do Carrinho --}}
            <div class="px-3 py-2 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-ink-800 dark:to-ink-800/50 border-b border-gray-200 dark:border-ink-700 shrink-0">

                <div class="flex justify-between items-center mb-2">
                    <div class="flex items-center gap-2">
                        <svg class="size-4 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.4 5.6A1 1 0 006.6 20h10.8a1 1 0 001-1.4L17 13M9 20a1 1 0 100 2 1 1 0 000-2zm8 0a1 1 0 100 2 1 1 0 000-2z"/>
                        </svg>

                        <span class="font-bold text-sm text-ink-900 dark:text-ink-50">
                            Carrinho
                            <span class="font-normal text-ink-500">
                                ({{ number_format($this->totalItens, 0) }})
                            </span>
                        </span>
                    </div>

                    @if(count($carrinho) > 0)
                        <button wire:click="limparCarrinho"
                            wire:confirm="Limpar carrinho?"
                            class="text-xs text-red-500 hover:text-red-700 transition-colors">
                            Limpar
                        </button>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="text-[11px] text-ink-500 font-medium">Mesa/Comanda</label>
                        <input type="text"
                            wire:model.live.debounce.500ms="mesa"
                            class="w-full mt-1 px-2 py-1.5 text-sm border border-gray-200 dark:border-ink-700 rounded-lg bg-white dark:bg-ink-800 focus:outline-none focus:ring-2 focus:ring-ink-500 transition-all"
                            placeholder="Nº mesa">
                    </div>

                    <div>
                        <label class="text-[11px] text-ink-500 font-medium">Cliente</label>
                        <select wire:model="clienteId"
                            class="w-full mt-1 px-2 py-1.5 text-sm border border-gray-200 dark:border-ink-700 rounded-lg bg-white dark:bg-ink-800 focus:outline-none focus:ring-2 focus:ring-ink-500 transition-all">
                            <option value="">Consumidor</option>

                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Itens do Carrinho (Totalmente visível, com altura máxima) --}}
            <div class="flex-1 min-h-0 overflow-y-auto divide-y divide-gray-100 dark:divide-ink-700 scrollbar-thin">
                @forelse($carrinho as $chave => $item)
                    <div class="px-3 py-2 hover:bg-gray-50 dark:hover:bg-ink-800/50 transition-colors" wire:key="item-{{ $chave }}">

                        <div class="flex justify-between items-start gap-2">
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-sm text-ink-900 dark:text-ink-100 truncate">
                                    {{ $item['nome'] }}
                                </p>

                                <p class="text-xs text-ink-500">
                                    {{ $item['preco_formatado'] }}
                                </p>
                            </div>

                            <button wire:click="removerProduto({{ $item['id'] }})"
                                class="text-red-400 hover:text-red-600 transition-colors">
                                ✕
                            </button>
                        </div>

                        <div class="flex justify-between items-center mt-2">
                            <div class="flex items-center gap-1">
                                <button wire:click="atualizarQuantidade({{ $item['id'] }}, {{ $item['quantidade'] - 1 }})"
                                    class="size-6 rounded-md bg-gray-100 hover:bg-gray-200 dark:bg-ink-700 dark:hover:bg-ink-600 text-ink-700 dark:text-ink-100 flex items-center justify-center">
                                    −
                                </button>

                                <span class="w-12 text-center text-sm font-medium">
                                    {{ number_format($item['quantidade'], $item['quantidade'] == intval($item['quantidade']) ? 0 : 3, ',', '.') }}
                                </span>

                                <button wire:click="atualizarQuantidade({{ $item['id'] }}, {{ $item['quantidade'] + 1 }})"
                                    class="size-6 rounded-md bg-gray-100 hover:bg-gray-200 dark:bg-ink-700 dark:hover:bg-ink-600 text-ink-700 dark:text-ink-100 flex items-center justify-center">
                                    +
                                </button>
                            </div>

                            <p class="font-bold text-sm text-ink-900 dark:text-ink-100">
                                R$ {{ number_format($item['subtotal'], 2, ',', '.') }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="h-full flex flex-col items-center justify-center text-center py-10">
                        <svg class="size-10 text-ink-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>

                        <p class="text-sm text-ink-400">Carrinho vazio</p>
                        <p class="text-xs text-ink-400 mt-1">Clique em um produto para adicionar</p>
                    </div>
                @endforelse
            </div>

          {{-- Total e Botão Finalizar --}}
<div class="shrink-0 border-t border-gray-200 dark:border-ink-700 bg-gray-50 dark:bg-ink-800/60 p-3">

    @php
        $totalPago = round(collect($pagamentos)->sum('valor'), 2);
        $pendente = max(0, $this->totalCarrinho - $totalPago);
    @endphp

    <div class="flex items-center justify-between mb-3">
        <div>
            <p class="text-xs text-ink-500">Total da venda</p>
            <p class="text-[11px] text-ink-400">
                Pago: R$ {{ number_format($totalPago, 2, ',', '.') }}
            </p>
        </div>

        <div class="text-right">
            <p class="text-2xl font-black text-primary-600 leading-tight">
                R$ {{ number_format($this->totalCarrinho, 2, ',', '.') }}
            </p>

            <p class="text-xs font-semibold {{ $pendente > 0 ? 'text-red-600' : 'text-green-600' }}">
                Restante: R$ {{ number_format($pendente, 2, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- Botão Salvar Mesa (só aparece se tiver mesa preenchida) --}}
    @if($modoComanda && $mesa)
        <button wire:click="salvarComanda" 
            @if(empty($carrinho)) disabled @endif
            class="w-full mb-2 py-2.5 border-2 border-amber-500 text-amber-600 dark:text-amber-400 rounded-xl font-bold text-sm uppercase tracking-wider hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-all disabled:opacity-40">
            💾 Salvar Mesa ({{ $mesa }})
        </button>
    @endif

    {{-- Botão Finalizar Venda --}}
    <button @click="showPayment = true"
        @if(empty($carrinho)) disabled @endif
        class="w-full py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl font-black text-sm uppercase tracking-widest shadow-lg shadow-green-900/20 transition-all active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed">
        💳 Finalizar Venda (F8)
    </button>
</div>
        </div>
    </div>

    {{-- MODAL DE PAGAMENTO COM SPLIT --}}
    <div x-show="showPayment" 
        x-trap.noscroll="showPayment"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
        style="display: none;">
        
        <div @click.away="showPayment = false" class="bg-white dark:bg-ink-900 w-full max-w-4xl rounded-2xl shadow-2xl overflow-hidden border border-white/10">
            <div class="p-4 border-b border-gray-100 dark:border-ink-800 flex items-center justify-between bg-gradient-to-r from-primary-50 to-indigo-50 dark:from-primary-900/20 dark:to-indigo-900/20">
                <div class="flex items-center gap-2">
                    <div class="p-1.5 bg-primary-600 text-white rounded-lg">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-black text-sm uppercase tracking-widest">Finalizar Pagamento</h3>
                        <p class="text-[10px] text-ink-500">Divida o pagamento em diferentes formas</p>
                    </div>
                </div>
                <button @click="showPayment = false" class="p-2 hover:bg-gray-200 dark:hover:bg-ink-700 rounded-full transition-colors">
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    
                    {{-- Coluna Esquerda: Formas de Pagamento --}}
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <p class="text-[10px] font-black uppercase text-ink-400 tracking-widest">Escolha a forma</p>
                            <span class="text-xs font-bold text-ink-500 bg-gray-100 dark:bg-ink-800 px-2 py-0.5 rounded-full">
                                Total: R$ {{ number_format($this->totalCarrinho, 2, ',', '.') }}
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-2">
                            @foreach([
                                ['dinheiro', 'Dinheiro', '💰'],
                                ['cartao_credito', 'Crédito', '💳'],
                                ['cartao_debito', 'Débito', '💳'],
                                ['pix', 'PIX', '📱'],
                            ] as [$val, $label, $icon])
                            <button wire:click="$set('formaPagamento', '{{ $val }}')"
                                class="p-3 rounded-xl border-2 transition-all flex flex-col items-center gap-1
                                    {{ $formaPagamento === $val
                                        ? 'border-primary-600 bg-primary-50 dark:bg-primary-900/10 text-primary-600 shadow-md'
                                        : 'border-gray-100 dark:border-ink-800 hover:border-primary-200 text-ink-600 dark:text-ink-400' }}">
                                <span class="text-2xl">{{ $icon }}</span>
                                <span class="text-[11px] font-bold uppercase tracking-tighter">{{ $label }}</span>
                            </button>
                            @endforeach
                        </div>

                        {{-- Input para adicionar pagamento --}}
                        <div class="grid grid-cols-2 gap-3 mt-4 pt-2 border-t border-gray-100 dark:border-ink-800">
                            <div>
                                <label class="text-[10px] font-bold uppercase text-ink-400">Valor</label>
                                <input type="number" wire:model.live="valorPagamento" step="0.01"
                                    class="w-full px-3 py-2.5 border-2 border-gray-100 dark:border-ink-800 rounded-xl text-center font-bold text-lg focus:border-primary-600 outline-none transition-all"
                                    placeholder="0,00">
                            </div>
                            <div class="flex items-end">
                                <button wire:click="adicionarPagamento" 
                                    @if(empty($carrinho) || $valorPagamento <= 0) disabled @endif
                                    class="w-full py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-xl font-bold text-xs uppercase tracking-wider transition-all hover:from-amber-600 hover:to-orange-600 disabled:opacity-40 shadow-md">
                                    ➕ Adicionar Pagamento
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Coluna Direita: Resumo e Pagamentos --}}
                    <div class="space-y-4">
                        <p class="text-[10px] font-black uppercase text-ink-400 tracking-widest">Pagamentos Realizados</p>
                        
                        {{-- Lista de pagamentos já adicionados --}}
                        <div class="space-y-2 max-h-48 overflow-y-auto custom-scrollbar pr-1">
                            @forelse($pagamentos as $index => $pag)
                            <div class="flex items-center justify-between bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl px-3 py-2.5">
                                <div class="flex items-center gap-2">
                                    <span class="text-xl">{{ $pag['forma'] === 'dinheiro' ? '💰' : ($pag['forma'] === 'pix' ? '📱' : '💳') }}</span>
                                    <span class="text-sm font-bold capitalize">{{ str_replace('_', ' ', $pag['forma']) }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="font-black text-green-700 dark:text-green-400">R$ {{ number_format($pag['valor'], 2, ',', '.') }}</span>
                                    @if(isset($pag['troco']) && $pag['troco'] > 0)
                                    <span class="text-xs font-semibold text-amber-600 bg-amber-50 dark:bg-amber-900/20 px-2 py-0.5 rounded-full">
                                        troco R$ {{ number_format($pag['troco'], 2, ',', '.') }}
                                    </span>
                                    @endif
                                    <button wire:click="removerPagamento({{ $index }})" 
                                        class="text-red-400 hover:text-red-600 transition-colors text-xl leading-none">✕</button>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-8 text-ink-400 text-sm">
                                Nenhum pagamento adicionado
                            </div>
                            @endforelse
                        </div>

                        {{-- Totais --}}
                        @php
                            $totalPago = round(collect($pagamentos)->sum('valor'), 2);
                            $pendente = max(0, $this->totalCarrinho - $totalPago);
                        @endphp

                        <div class="bg-gray-50 dark:bg-ink-800/30 rounded-xl p-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-ink-600">💰 Subtotal</span>
                                <span class="font-bold">R$ {{ number_format($this->totalCarrinho, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-ink-600">✅ Pago</span>
                                <span class="font-bold text-green-600">R$ {{ number_format($totalPago, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm pt-2 border-t border-gray-200 dark:border-ink-700">
                                <span class="font-black text-ink-900 dark:text-ink-50">💵 Restante</span>
                                <span class="font-black text-xl {{ $pendente > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    R$ {{ number_format($pendente, 2, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Botão Finalizar --}}
                <div class="mt-6 pt-4 border-t border-gray-100 dark:border-ink-800">
                    <button wire:click="finalizarVenda" 
                        @if(empty($carrinho) || $pendente > 0) disabled @endif
                        class="w-full py-4 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-xl font-black text-sm uppercase tracking-widest shadow-lg shadow-green-900/20 transition-all disabled:opacity-40 disabled:cursor-not-allowed">
                        ✅ FINALIZAR VENDA {{ $pendente > 0 ? '(Faltam R$ ' . number_format($pendente, 2, ',', '.') . ')' : '' }}
                    </button>
                    @if($pendente > 0)
                    <p class="text-center text-xs text-amber-600 mt-2 font-medium">
                        Adicione mais pagamentos até completar o valor total
                    </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL PARA NF NA FINALIZAÇÃO --}}
    @if($mostrarModalNF)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-ink-900 rounded-2xl w-full max-w-md p-6 shadow-2xl transform transition-all animate-in zoom-in-95 duration-200">

                <div class="flex items-center gap-3 mb-4">
                    <div class="size-12 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center">
                        <svg class="size-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>

                    <h2 class="text-xl font-bold text-ink-900 dark:text-ink-50">
                        Emitir Nota Fiscal
                    </h2>
                </div>

                <p class="text-sm text-ink-500 dark:text-ink-400 mb-4">
                    Preencha os dados do cliente para emitir a nota fiscal
                </p>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">
                            CPF / CNPJ *
                        </label>

                        <input type="text"
                            wire:model="cpfCnpjNF"
                            class="w-full px-4 py-2.5 border border-gray-200 dark:border-ink-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-ink-500 transition-all"
                            placeholder="000.000.000-00 ou 00.000.000/0000-00">

                        @error('cpfCnpjNF')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">
                            Nome do Cliente *
                        </label>

                        <input type="text"
                            wire:model="nomeClienteNF"
                            class="w-full px-4 py-2.5 border border-gray-200 dark:border-ink-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-ink-500 transition-all"
                            placeholder="Nome completo">

                        @error('nomeClienteNF')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="finalizarSemNF"
                        class="px-5 py-2.5 border border-gray-300 dark:border-ink-600 rounded-xl text-ink-700 dark:text-ink-300 hover:bg-gray-50 dark:hover:bg-ink-800 transition-all">
                        Não emitir
                    </button>

                    <button wire:click="emitirNotaDaVenda"
                        class="px-5 py-2.5 bg-gradient-to-r from-ink-800 to-ink-900 text-white rounded-xl font-medium hover:shadow-lg transition-all">
                        Emitir Nota
                    </button>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
        <style>
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

            .custom-scrollbar::-webkit-scrollbar {
                width: 4px;
            }
            
            .custom-scrollbar::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 10px;
            }
            
            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #c1c1c1;
                border-radius: 10px;
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

            @media (max-height: 760px) and (min-width: 1024px) {
                .pdv-card-produto {
                    padding: 0.35rem;
                }

                .pdv-icone {
                    height: 2.5rem;
                }

                .pdv-card-produto p {
                    font-size: 10px;
                }
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Forçar foco no campo código após o carregamento completo
                setTimeout(function() {
                    const campoCodigo = document.getElementById('campo-codigo');
                    if (campoCodigo) {
                        campoCodigo.focus();
                    }
                }, 100);
                
                document.addEventListener('keydown', function (e) {
                    // Ignora se está digitando em input/textarea/select
                    const target = e.target;
                    const isInput = target.tagName === 'INPUT' && target.type !== 'hidden';
                    const isSelect = target.tagName === 'SELECT';
                    const isTextarea = target.tagName === 'TEXTAREA';
                    
                    if (isInput || isSelect || isTextarea) {
                        // Permite F2, F5, F6, F8 mesmo dentro de inputs
                        if (!['F2', 'F5', 'F6', 'F8'].includes(e.key)) return;
                    }

                    if (e.key === 'F2') {
                        e.preventDefault();
                        const campoCodigo = document.getElementById('campo-codigo');
                        if (campoCodigo) campoCodigo.focus();
                    }

                    if (e.key === 'F5') {
                        e.preventDefault();
                        const component = document.querySelector('[wire\\:id]');
                        if (!component) return;
                        const wireId = component.getAttribute('wire:id');
                        const wire = Livewire.find(wireId);
                        if (wire) wire.call('acaoF5');
                    }

                    if (e.key === 'F6') {
                        e.preventDefault();
                        if (confirm('Nova venda?')) {
                            const component = document.querySelector('[wire\\:id]');
                            if (!component) return;
                            const wireId = component.getAttribute('wire:id');
                            const wire = Livewire.find(wireId);
                            if (wire) {
                                wire.set('mesa', '');
                                wire.set('modoComanda', false);
                                wire.set('comandaId', null);
                                wire.call('limparCarrinho');
                            }
                        }
                    }

                    if (e.key === 'F8') {
                        e.preventDefault();
                        // Encontrar o Alpine component
                        const alpineRoot = document.querySelector('[x-data]');
                        if (alpineRoot && alpineRoot.__x && alpineRoot.__x.$data) {
                            alpineRoot.__x.$data.showPayment = true;
                        }
                    }
                });

                document.addEventListener('focar-codigo', function () {
                    const campoCodigo = document.getElementById('campo-codigo');
                    if (campoCodigo) campoCodigo.focus();
                });
            });
        </script>
    @endpush
</div>