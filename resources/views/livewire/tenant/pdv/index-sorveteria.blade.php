<div x-data="{ 
        showPayment: false,
        init() {
            this.$nextTick(() => {
                const campoCodigo = document.getElementById('campo-codigo');
                if (campoCodigo) {
                    campoCodigo.focus();
                }
            });
            document.addEventListener('keydown', this.handleKeydown.bind(this));
        },
        handleKeydown(e) {
            const target = e.target;
            const isInput = target.tagName === 'INPUT' && target.type !== 'hidden';
            const isSelect = target.tagName === 'SELECT';
            const isTextarea = target.tagName === 'TEXTAREA';
            
            if (isInput || isSelect || isTextarea) {
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
    class="min-h-screen lg:h-screen lg:overflow-hidden bg-gradient-to-br from-pink-50 to-blue-50 dark:from-slate-900 dark:to-slate-950 p-2 sm:p-3">

    {{-- Cabeçalho --}}
    <div class="mb-3 flex justify-between items-center">
        <div>
            <h1 class="text-xl sm:text-2xl font-black bg-gradient-to-r from-pink-600 to-purple-600 dark:from-pink-400 dark:to-purple-400 bg-clip-text text-transparent">
                🍦 Sorveteria PDV
            </h1>
            <p class="text-[10px] font-bold uppercase tracking-wider text-pink-500/70 mt-0.5">Vendas por Peso e Unidade</p>
        </div>

        <div class="hidden sm:block text-right">
            <div class="flex items-center gap-2 text-xs text-slate-500 bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm px-3 py-1.5 rounded-full border border-pink-100 dark:border-pink-900/30">
                <span class="font-mono font-bold text-pink-600">F2</span> <span>🔍 Buscar</span>
                <span class="w-px h-3 bg-slate-300"></span>
                <span class="font-mono font-bold text-pink-600">F5</span> <span>✅ Finalizar</span>
                <span class="w-px h-3 bg-slate-300"></span>
                <span class="font-mono font-bold text-pink-600">F6</span> <span>🔄 Novo</span>
                <span class="w-px h-3 bg-slate-300"></span>
                <span class="font-mono font-bold text-pink-600">F8</span> <span>💳 Pagar</span>
            </div>
        </div>
    </div>

    {{-- Status do Caixa --}}
    @if($this->caixaAberto)
        <div class="mb-3 flex flex-wrap items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2 bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm border border-pink-100 dark:border-pink-900/30 rounded-xl shadow-sm">
            <div class="relative">
                <span class="absolute inset-0 flex items-center justify-center">
                    <span class="size-2 rounded-full bg-pink-500 animate-ping"></span>
                </span>
                <span class="relative size-2 rounded-full bg-pink-500 block"></span>
            </div>
            <span class="font-bold text-pink-700 dark:text-pink-400 text-xs uppercase tracking-tight">Caixa Aberto</span>
            <span class="text-pink-200 hidden sm:inline">|</span>
            <span class="text-xs font-medium text-slate-600 dark:text-slate-400">
                {{ $this->caixaAberto->aberto_em->format('H:i') }} • {{ $this->caixaAberto->operador->name ?? 'N/A' }}
            </span>
            <span class="ml-auto text-xs font-black text-pink-600 dark:text-pink-400 bg-pink-50 dark:bg-pink-900/20 px-2 py-1 rounded-lg">
                R$ {{ number_format($this->caixaAberto->total_vendas, 2, ',', '.') }}
            </span>
        </div>
    @else
        <div class="mb-3 flex items-center justify-between gap-3 px-3 sm:px-4 py-2 bg-red-50 dark:bg-red-900/20 border border-red-200 rounded-xl shadow-sm">
            <div class="flex items-center gap-3">
                <span class="size-2 rounded-full bg-red-500 animate-pulse"></span>
                <span class="font-bold text-red-700 dark:text-red-400 text-xs uppercase">⚠️ Caixa Fechado</span>
            </div>
            <a href="{{ route('tenant.caixa') }}" class="px-3 py-1 bg-red-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">Abrir</a>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-3 lg:h-[calc(100vh-135px)] min-h-0">
        {{-- COLUNA ESQUERDA — Produtos --}}
        <div class="xl:col-span-7 bg-white/70 dark:bg-slate-900/70 backdrop-blur-md rounded-2xl border border-white dark:border-slate-800 shadow-xl overflow-hidden flex flex-col min-h-0">
            <div class="p-3 space-y-3 shrink-0">
                {{-- Busca e Peso --}}
                <div class="grid grid-cols-12 gap-2">
                    <div class="col-span-12 sm:col-span-5 relative">
                        <input type="text" wire:model.live.debounce.300ms="busca" placeholder="Buscar sabor ou item..."
                            class="w-full pl-4 pr-3 py-2.5 border-none bg-slate-100 dark:bg-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-pink-500 transition-all">
                    </div>

                    <div class="col-span-7 sm:col-span-4 relative">
                        <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                            <span class="text-xs font-bold text-pink-500">⚖️</span>
                        </div>
                        <input type="text" wire:model="peso" placeholder="Peso (ex: 0.500)"
                            class="w-full pl-9 pr-3 py-2.5 border-none bg-pink-50 dark:bg-pink-900/20 rounded-xl text-sm font-bold text-pink-700 dark:text-pink-300 focus:ring-2 focus:ring-pink-500">
                    </div>

                    <div class="col-span-5 sm:col-span-3 relative">
                        <input type="text" wire:model="codigoProduto" wire:keydown.enter="buscarPorCodigo" placeholder="Cód/ID" id="campo-codigo"
                            class="w-full px-3 py-2.5 border-none bg-slate-100 dark:bg-slate-800 rounded-xl text-sm focus:ring-2 focus:ring-pink-500">
                    </div>
                </div>

                {{-- Categorias Temáticas --}}
                <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-thin">
                    <button wire:click="selecionarCategoria(null)"
                        class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest whitespace-nowrap transition-all
                            {{ !$categoriaSelecionada ? 'bg-pink-600 text-white shadow-lg' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-100 dark:border-slate-700' }}">
                        🍦 TODOS
                    </button>
                    @foreach($categorias as $categoria)
                        <button wire:click="selecionarCategoria({{ $categoria->id }})"
                            class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest whitespace-nowrap transition-all
                                {{ $categoriaSelecionada == $categoria->id ? 'bg-pink-600 text-white shadow-lg' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-100 dark:border-slate-700' }}">
                            {{ $categoria->icone ?? '🍨' }} {{ $categoria->nome }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Grid de Produtos Estilo "Candy" --}}
            <div class="flex-1 min-h-0 overflow-y-auto p-3 pt-0 scrollbar-thin">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 content-start">
                    @forelse($produtos as $produto)
                        <button wire:click="adicionarProduto({{ $produto->id }})" wire:key="prod-{{ $produto->id }}"
                            class="group bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-3 text-center hover:border-pink-300 dark:hover:border-pink-700 hover:shadow-xl transition-all active:scale-95">
                            <div class="mb-2 mx-auto size-14 bg-gradient-to-br from-pink-50 to-blue-50 dark:from-slate-700 dark:to-slate-600 rounded-full flex items-center justify-center text-3xl group-hover:rotate-12 transition-transform">
                                {{ $produto->icone ?? '🍨' }}
                            </div>
                            <p class="font-bold text-[11px] leading-tight line-clamp-2 text-slate-800 dark:text-slate-100 mb-1">
                                {{ $produto->nome }}
                            </p>
                            <p class="text-xs font-black text-pink-600">
                                R$ {{ number_format($produto->preco_atual, 2, ',', '.') }}
                            </p>
                        </button>
                    @empty
                        <div class="col-span-full text-center py-12 opacity-50">
                            <span class="text-4xl">🍦</span>
                            <p class="text-xs font-bold uppercase mt-2">Nenhum sabor encontrado</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- COLUNA DIREITA — Carrinho --}}
        <div class="xl:col-span-5 bg-white/70 dark:bg-slate-900/70 backdrop-blur-md rounded-2xl border border-white dark:border-slate-800 shadow-xl overflow-hidden flex flex-col min-h-0">
            <div class="p-4 border-b border-slate-100 dark:border-slate-800 shrink-0">
                <div class="flex justify-between items-center mb-3">
                    <h3 class="font-black text-xs uppercase tracking-widest text-slate-800 dark:text-slate-100">🛒 Pedido Atual</h3>
                    @if(count($carrinho) > 0)
                        <button wire:click="limparCarrinho" wire:confirm="Limpar pedido?" class="text-[10px] font-bold text-red-500 uppercase">Limpar</button>
                    @endif
                </div>
                
                <div class="grid grid-cols-2 gap-2">
                    <input type="text" wire:model.live.debounce.500ms="mesa" placeholder="Nº Mesa"
                        class="px-3 py-2 bg-slate-100 dark:bg-slate-800 border-none rounded-xl text-xs focus:ring-1 focus:ring-pink-500">
                    <select wire:model="clienteId" class="px-3 py-2 bg-slate-100 dark:bg-slate-800 border-none rounded-xl text-xs focus:ring-1 focus:ring-pink-500">
                        <option value="">Consumidor</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex-1 min-h-0 overflow-y-auto p-2 divide-y divide-slate-50 dark:divide-slate-800 scrollbar-thin">
                @forelse($carrinho as $chave => $item)
                    <div class="py-3 px-2 group" wire:key="item-{{ $chave }}">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <p class="font-bold text-sm text-slate-800 dark:text-slate-100">{{ $item['nome'] }}</p>
                                <p class="text-[10px] text-slate-500">{{ $item['preco_formatado'] }} @if($item['quantidade'] > 1) x {{ $item['quantidade'] }} @endif</p>
                            </div>
                            <button wire:click="removerProduto({{ $item['id'] }})" class="text-slate-300 hover:text-red-500 transition-colors">✕</button>
                        </div>
                        <div class="flex justify-between items-center mt-2">
                            <div class="flex items-center bg-slate-100 dark:bg-slate-800 rounded-lg p-1">
                                <button wire:click="atualizarQuantidade({{ $item['id'] }}, {{ $item['quantidade'] - 1 }})" class="size-5 flex items-center justify-center text-slate-500 hover:text-pink-600">-</button>
                                <span class="px-3 text-xs font-black">{{ number_format($item['quantidade'], $item['quantidade'] == intval($item['quantidade']) ? 0 : 3, ',', '.') }}</span>
                                <button wire:click="atualizarQuantidade({{ $item['id'] }}, {{ $item['quantidade'] + 1 }})" class="size-5 flex items-center justify-center text-slate-500 hover:text-pink-600">+</button>
                            </div>
                            <p class="font-black text-sm text-pink-600">R$ {{ number_format($item['subtotal'], 2, ',', '.') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="h-full flex flex-col items-center justify-center opacity-30 py-10">
                        <span class="text-5xl mb-2">🛍️</span>
                        <p class="text-[10px] font-black uppercase">Aguardando itens...</p>
                    </div>
                @endforelse
            </div>

            <div class="p-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800 shrink-0">
                @php
                    $totalPago = round(collect($pagamentos)->sum('valor'), 2);
                    $pendente = max(0, $this->totalCarrinho - $totalPago);
                @endphp
                <div class="flex justify-between items-end mb-4">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total do Pedido</p>
                        <p class="text-3xl font-black text-slate-800 dark:text-white leading-none">R$ {{ number_format($this->totalCarrinho, 2, ',', '.') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-bold {{ $pendente > 0 ? 'text-red-500' : 'text-green-500' }} uppercase">Falta: R$ {{ number_format($pendente, 2, ',', '.') }}</p>
                    </div>
                </div>

                <button @click="showPayment = true" @if(empty($carrinho)) disabled @endif
                    class="w-full py-4 bg-gradient-to-r from-pink-600 to-purple-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg shadow-pink-500/30 hover:shadow-pink-500/50 transition-all active:scale-95 disabled:opacity-50">
                    💳 Finalizar Pagamento (F8)
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL DE PAGAMENTO (Mantendo estrutura original com ajuste visual) --}}
    <div x-show="showPayment" x-trap.noscroll="showPayment" style="display: none;"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div @click.away="showPayment = false" class="bg-white dark:bg-slate-900 w-full max-w-4xl rounded-3xl shadow-2xl overflow-hidden border border-white/10">
            <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-pink-50/30 dark:bg-pink-900/10">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-pink-600 text-white rounded-xl">💰</div>
                    <div>
                        <h3 class="font-black text-sm uppercase tracking-widest">Pagamento</h3>
                        <p class="text-[10px] text-slate-500">Selecione a forma de pagamento</p>
                    </div>
                </div>
                <button @click="showPayment = false" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="space-y-4">
                    <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Métodos</p>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach([['dinheiro', 'Dinheiro', '💰'], ['cartao_credito', 'Crédito', '💳'], ['cartao_debito', 'Débito', '💳'], ['pix', 'PIX', '📱']] as [$val, $label, $icon])
                        <button wire:click="$set('formaPagamento', '{{ $val }}')"
                            class="p-4 rounded-2xl border-2 transition-all flex flex-col items-center gap-2
                                {{ $formaPagamento === $val ? 'border-pink-600 bg-pink-50 dark:bg-pink-900/20 text-pink-600' : 'border-slate-50 dark:border-slate-800 hover:border-pink-200' }}">
                            <span class="text-2xl">{{ $icon }}</span>
                            <span class="text-[10px] font-black uppercase">{{ $label }}</span>
                        </button>
                        @endforeach
                    </div>
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                        <label class="text-[10px] font-black text-slate-400 uppercase">Valor a Receber</label>
                        <div class="flex gap-2 mt-1">
                            <input type="number" wire:model.live="valorPagamento" step="0.01"
                                class="flex-1 px-4 py-3 bg-slate-100 dark:bg-slate-800 border-none rounded-xl font-black text-lg focus:ring-2 focus:ring-pink-500">
                            <button wire:click="adicionarPagamento" @if(empty($carrinho) || $valorPagamento <= 0) disabled @endif
                                class="px-6 bg-pink-600 text-white rounded-xl font-black text-xs uppercase shadow-md">Add</button>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Resumo</p>
                    <div class="space-y-2 max-h-48 overflow-y-auto scrollbar-thin pr-1">
                        @forelse($pagamentos as $index => $pag)
                        <div class="flex items-center justify-between bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800 rounded-xl px-4 py-3">
                            <span class="text-xs font-bold capitalize">{{ str_replace('_', ' ', $pag['forma']) }}</span>
                            <div class="flex items-center gap-3">
                                <span class="font-black text-green-700 dark:text-green-400 text-sm">R$ {{ number_format($pag['valor'], 2, ',', '.') }}</span>
                                <button wire:click="removerPagamento({{ $index }})" class="text-red-400">✕</button>
                            </div>
                        </div>
                        @empty
                        <p class="text-center py-8 text-xs text-slate-400 font-bold uppercase">Nenhum pagamento</p>
                        @endforelse
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-4 space-y-2">
                        <div class="flex justify-between text-xs font-bold">
                            <span class="text-slate-500 uppercase">Subtotal</span>
                            <span>R$ {{ number_format($this->totalCarrinho, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-xs font-bold pt-2 border-t border-slate-200 dark:border-slate-700">
                            <span class="text-slate-800 dark:text-white uppercase">Restante</span>
                            <span class="text-lg font-black {{ $pendente > 0 ? 'text-red-600' : 'text-green-600' }}">R$ {{ number_format($pendente, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 bg-slate-50 dark:bg-slate-800/30 border-t border-slate-100 dark:border-slate-800">
                <button wire:click="finalizarVenda" @if(empty($carrinho) || $pendente > 0) disabled @endif
                    class="w-full py-4 bg-green-600 hover:bg-green-700 text-white rounded-2xl font-black text-sm uppercase tracking-widest shadow-lg shadow-green-900/20 transition-all disabled:opacity-40">
                    ✅ Finalizar e Imprimir
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL NF (Simplificado para manter foco no layout) --}}
    @if($mostrarModalNF)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-md p-6 shadow-2xl">
                <h2 class="text-lg font-black uppercase tracking-widest mb-4">Emitir Nota Fiscal</h2>
                <div class="space-y-4">
                    <input type="text" wire:model="cpfCnpjNF" placeholder="CPF / CNPJ" class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-800 border-none rounded-xl text-sm">
                    <input type="text" wire:model="nomeClienteNF" placeholder="Nome do Cliente" class="w-full px-4 py-3 bg-slate-100 dark:bg-slate-800 border-none rounded-xl text-sm">
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="finalizarSemNF" class="text-xs font-bold uppercase text-slate-500">Sem NF</button>
                    <button wire:click="emitirNotaDaVenda" class="px-6 py-3 bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 rounded-xl text-xs font-black uppercase">Emitir</button>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
        <style>
            .scrollbar-thin::-webkit-scrollbar { width: 5px; height: 5px; }
            .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
            .scrollbar-thin::-webkit-scrollbar-thumb { background: rgba(219, 39, 119, 0.2); border-radius: 10px; }
            .dark .scrollbar-thin::-webkit-scrollbar-thumb { background: rgba(219, 39, 119, 0.4); }
        </style>
    @endpush
</div>