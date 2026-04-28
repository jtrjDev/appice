<div x-data="{ 
        showPayment: false,
        init() {
            this.$nextTick(() => {
                const campoCodigo = document.getElementById('campo-codigo');
                if (campoCodigo) campoCodigo.focus();
            });
            document.addEventListener('keydown', this.handleKeydown.bind(this));
        },
        handleKeydown(e) {
            const target = e.target;
            const isInput = target.tagName === 'INPUT' && target.type !== 'hidden';
            if (isInput || target.tagName === 'SELECT' || target.tagName === 'TEXTAREA') {
                if (!['F2', 'F5', 'F6', 'F8'].includes(e.key)) return;
            }
            if (e.key === 'F2') { e.preventDefault(); document.getElementById('campo-codigo')?.focus(); }
            if (e.key === 'F5') { e.preventDefault(); Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'))?.call('acaoF5'); }
            if (e.key === 'F6') { e.preventDefault(); if (confirm('Nova venda?')) { const wire = Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')); if (wire) { wire.set('mesa', ''); wire.set('modoComanda', false); wire.call('limparCarrinho'); } } }
            if (e.key === 'F8') { e.preventDefault(); this.showPayment = true; }
        }
    }"
    class="min-h-screen lg:h-screen lg:overflow-hidden bg-[#1a1a1a] text-slate-200 p-2 sm:p-3">

    {{-- Cabeçalho Pizzaria --}}
    <div class="mb-3 flex justify-between items-center border-b border-orange-900/30 pb-2">
        <div class="flex items-center gap-3">
            <div class="size-10 bg-orange-600 rounded-lg flex items-center justify-center text-2xl shadow-lg shadow-orange-600/20">🍕</div>
            <div>
                <h1 class="text-xl font-black text-white uppercase tracking-tighter">Pizzaria Express</h1>
                <div class="flex gap-2">
                    <span class="text-[9px] font-bold bg-orange-600/20 text-orange-500 px-1.5 py-0.5 rounded">MODO DELIVERY</span>
                    <span class="text-[9px] font-bold bg-green-600/20 text-green-500 px-1.5 py-0.5 rounded">CAIXA OPERANTE</span>
                </div>
            </div>
        </div>

        <div class="hidden sm:flex items-center gap-4 text-[10px] font-bold text-slate-500">
            <div class="flex items-center gap-1.5"><span class="bg-slate-800 text-slate-300 px-1.5 py-0.5 rounded">F2</span> BUSCAR</div>
            <div class="flex items-center gap-1.5"><span class="bg-slate-800 text-slate-300 px-1.5 py-0.5 rounded">F8</span> PAGAR</div>
            <div class="w-px h-4 bg-slate-800"></div>
            <div class="text-orange-500">{{ now()->format('H:i') }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-3 lg:h-[calc(100vh-100px)] min-h-0">
        {{-- COLUNA ESQUERDA — Cardápio --}}
        <div class="xl:col-span-8 flex flex-col min-h-0 gap-3">
            
            {{-- Filtros e Busca --}}
            <div class="bg-slate-900/50 rounded-xl p-3 border border-slate-800 flex flex-wrap gap-3 items-center">
                <div class="flex-1 min-w-[200px] relative">
                    <input type="text" wire:model.live.debounce.300ms="busca" placeholder="Qual pizza vamos pedir hoje?"
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-orange-600 transition-all">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">🔍</span>
                </div>

                <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-thin">
                    <button wire:click="selecionarCategoria(null)" 
                        class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all
                        {{ !$categoriaSelecionada ? 'bg-orange-600 text-white' : 'bg-slate-800 text-slate-400 hover:bg-slate-700' }}">
                        🔥 TODAS
                    </button>
                    @foreach($categorias as $categoria)
                        <button wire:click="selecionarCategoria({{ $categoria->id }})"
                            class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all
                            {{ $categoriaSelecionada == $categoria->id ? 'bg-orange-600 text-white' : 'bg-slate-800 text-slate-400 hover:bg-slate-700' }}">
                            {{ $categoria->nome }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Grid de Pizzas --}}
            <div class="flex-1 overflow-y-auto pr-1 scrollbar-thin">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    @forelse($produtos as $produto)
                        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-3 flex flex-col group hover:border-orange-600/50 transition-all">
                            <div class="relative mb-3">
                                <div class="aspect-square bg-slate-800 rounded-xl flex items-center justify-center text-4xl group-hover:scale-110 transition-transform">
                                    {{ $produto->icone ?? '🍕' }}
                                </div>
                                @if($pdvConfig['permite_meio'])
                                <button wire:click="$set('meiaPorcao', true)" class="absolute -top-1 -right-1 bg-orange-600 text-white text-[8px] font-black px-1.5 py-0.5 rounded-md shadow-lg">1/2</button>
                                @endif
                            </div>
                            <h4 class="font-bold text-xs text-white line-clamp-1 mb-1">{{ $produto->nome }}</h4>
                            <p class="text-[10px] text-slate-500 mb-3 line-clamp-2">Massa artesanal, molho de tomate...</p>
                            
                            <div class="mt-auto flex items-center justify-between gap-2">
                                <span class="font-black text-orange-500 text-xs">R$ {{ number_format($produto->preco_atual, 2, ',', '.') }}</span>
                                <button wire:click="adicionarProduto({{ $produto->id }})" 
                                    class="bg-white text-black size-7 rounded-lg flex items-center justify-center font-black hover:bg-orange-600 hover:text-white transition-colors">+</button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-20 text-center opacity-20">
                            <span class="text-6xl">🍕</span>
                            <p class="mt-4 font-black uppercase">Nenhuma pizza no forno</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Configurações de Pizza (Tamanhos/Adicionais) --}}
            <div class="bg-slate-900/80 border border-slate-800 rounded-xl p-3 grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[9px] font-black text-slate-500 uppercase mb-2 block">Tamanho da Pizza</label>
                    <div class="flex gap-2">
                        @foreach(['P', 'M', 'G', 'GG'] as $tam)
                            <button class="flex-1 py-1.5 rounded-lg border border-slate-700 text-[10px] font-bold hover:bg-slate-800 transition-all">
                                {{ $tam }}
                            </button>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="text-[9px] font-black text-slate-500 uppercase mb-2 block">Opções Rápidas</label>
                    <div class="flex gap-2">
                        <button class="flex-1 py-1.5 rounded-lg bg-green-600/10 text-green-500 border border-green-600/20 text-[10px] font-bold">+ Borda</button>
                        <button class="flex-1 py-1.5 rounded-lg bg-blue-600/10 text-blue-500 border border-blue-600/20 text-[10px] font-bold">Sem Cebola</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- COLUNA DIREITA — Checkout --}}
        <div class="xl:col-span-4 flex flex-col min-h-0 bg-slate-900 rounded-2xl border border-slate-800 overflow-hidden shadow-2xl">
            <div class="p-4 border-b border-slate-800">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-black text-sm text-white uppercase tracking-tighter">Resumo do Pedido</h3>
                    <span class="text-[10px] font-bold text-slate-500">#{{ date('mdHi') }}</span>
                </div>
                
                <div class="space-y-3">
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs">🏠</span>
                        <input type="text" wire:model.live.debounce.500ms="mesa" placeholder="Mesa ou Endereço"
                            class="w-full pl-9 pr-3 py-2 bg-slate-800 border-none rounded-xl text-xs focus:ring-1 focus:ring-orange-600">
                    </div>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs">👤</span>
                        <select wire:model="clienteId" class="w-full pl-9 pr-3 py-2 bg-slate-800 border-none rounded-xl text-xs focus:ring-1 focus:ring-orange-600">
                            <option value="">Cliente Balcão</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-4 scrollbar-thin">
                @forelse($carrinho as $chave => $item)
                    <div class="flex gap-3 group" wire:key="item-{{ $chave }}">
                        <div class="size-10 bg-slate-800 rounded-lg flex items-center justify-center text-lg">{{ $item['icone'] ?? '🍕' }}</div>
                        <div class="flex-1">
                            <div class="flex justify-between">
                                <h5 class="font-bold text-xs text-slate-200">{{ $item['nome'] }}</h5>
                                <button wire:click="removerProduto({{ $item['id'] }})" class="text-slate-600 hover:text-red-500">✕</button>
                            </div>
                            <div class="flex justify-between items-center mt-1">
                                <div class="flex items-center gap-2">
                                    <button wire:click="atualizarQuantidade({{ $item['id'] }}, {{ $item['quantidade'] - 1 }})" class="text-slate-500 hover:text-white">➖</button>
                                    <span class="text-xs font-black text-orange-500">{{ (int)$item['quantidade'] }}</span>
                                    <button wire:click="atualizarQuantidade({{ $item['id'] }}, {{ $item['quantidade'] + 1 }})" class="text-slate-500 hover:text-white">➕</button>
                                </div>
                                <span class="text-xs font-black text-white">R$ {{ number_format($item['subtotal'], 2, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="h-full flex flex-col items-center justify-center opacity-10">
                        <span class="text-5xl">🛒</span>
                        <p class="mt-2 text-[10px] font-black uppercase">Carrinho Vazio</p>
                    </div>
                @endforelse
            </div>

            <div class="p-4 bg-slate-950/50 border-t border-slate-800">
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-[10px] font-bold text-slate-500 uppercase">
                        <span>Subtotal</span>
                        <span>R$ {{ number_format($this->totalCarrinho, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-xs font-black text-white uppercase">
                        <span>Total Geral</span>
                        <span class="text-lg text-orange-500">R$ {{ number_format($this->totalCarrinho, 2, ',', '.') }}</span>
                    </div>
                </div>

                <button @click="showPayment = true" @if(empty($carrinho)) disabled @endif
                    class="w-full py-4 bg-orange-600 hover:bg-orange-700 text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-xl shadow-orange-600/20 transition-all active:scale-95 disabled:opacity-30">
                    💳 FINALIZAR PEDIDO (F8)
                </button>
            </div>
        </div>
    </div>

    {{-- Modal Pagamento Estilo Dark --}}
    <div x-show="showPayment" x-trap.noscroll="showPayment" style="display: none;"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md">
        <div @click.away="showPayment = false" class="bg-slate-900 w-full max-w-4xl rounded-3xl border border-slate-800 shadow-2xl overflow-hidden">
            <div class="p-6 border-b border-slate-800 flex justify-between items-center bg-slate-950/50">
                <h3 class="font-black text-sm uppercase tracking-widest text-orange-500">Finalizar Venda</h3>
                <button @click="showPayment = false" class="text-slate-500 hover:text-white">FECHAR [ESC]</button>
            </div>
            
            <div class="p-8 grid grid-cols-1 lg:grid-cols-2 gap-10">
                <div class="space-y-6">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Forma de Pagamento</p>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach([['dinheiro', 'Dinheiro', '💵'], ['cartao_credito', 'Crédito', '💳'], ['cartao_debito', 'Débito', '💳'], ['pix', 'PIX', '📱']] as [$val, $label, $icon])
                        <button wire:click="$set('formaPagamento', '{{ $val }}')"
                            class="p-5 rounded-2xl border-2 transition-all flex flex-col items-center gap-2
                            {{ $formaPagamento === $val ? 'border-orange-600 bg-orange-600/10 text-orange-500' : 'border-slate-800 hover:border-slate-700 text-slate-500' }}">
                            <span class="text-2xl">{{ $icon }}</span>
                            <span class="text-[10px] font-black uppercase">{{ $label }}</span>
                        </button>
                        @endforeach
                    </div>
                    
                    <div class="pt-6 border-t border-slate-800">
                        <div class="flex gap-3">
                            <div class="flex-1">
                                <label class="text-[9px] font-black text-slate-500 uppercase mb-1 block">Valor Recebido</label>
                                <input type="number" wire:model.live="valorPagamento" step="0.01"
                                    class="w-full px-4 py-3 bg-slate-800 border-none rounded-xl font-black text-white focus:ring-2 focus:ring-orange-600">
                            </div>
                            <div class="flex items-end">
                                <button wire:click="adicionarPagamento" class="px-8 py-3 bg-orange-600 text-white rounded-xl font-black text-xs uppercase shadow-lg shadow-orange-600/20">CONFIRMAR</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-950/50 rounded-3xl p-6 flex flex-col">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-6">Resumo Financeiro</p>
                    <div class="space-y-3 flex-1 overflow-y-auto max-h-40 scrollbar-thin pr-2">
                        @foreach($pagamentos as $index => $pag)
                        <div class="flex justify-between items-center bg-slate-800/50 p-3 rounded-xl border border-slate-700">
                            <span class="text-xs font-bold text-slate-300 capitalize">{{ str_replace('_', ' ', $pag['forma']) }}</span>
                            <div class="flex items-center gap-3">
                                <span class="font-black text-white text-sm">R$ {{ number_format($pag['valor'], 2, ',', '.') }}</span>
                                <button wire:click="removerPagamento({{ $index }})" class="text-red-500">✕</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-6 pt-6 border-t border-slate-800 space-y-3">
                        <div class="flex justify-between text-xs font-bold text-slate-500">
                            <span>TOTAL A PAGAR</span>
                            <span>R$ {{ number_format($this->totalCarrinho, 2, ',', '.') }}</span>
                        </div>
                        @php $pendente = max(0, $this->totalCarrinho - collect($pagamentos)->sum('valor')); @endphp
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-black text-white uppercase">RESTANTE</span>
                            <span class="text-2xl font-black {{ $pendente > 0 ? 'text-red-500' : 'text-green-500' }}">R$ {{ number_format($pendente, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-8 bg-slate-950/80 border-t border-slate-800">
                <button wire:click="finalizarVenda" @if(empty($carrinho) || $pendente > 0) disabled @endif
                    class="w-full py-5 bg-green-600 hover:bg-green-700 text-white rounded-2xl font-black text-sm uppercase tracking-widest shadow-xl shadow-green-600/20 transition-all disabled:opacity-20">
                    🚀 FINALIZAR E ENVIAR PARA COZINHA
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <style>
            .scrollbar-thin::-webkit-scrollbar { width: 4px; height: 4px; }
            .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
            .scrollbar-thin::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
            .scrollbar-thin::-webkit-scrollbar-thumb:hover { background: #ea580c; }
        </style>
    @endpush
</div>