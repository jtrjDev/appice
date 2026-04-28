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
                // Ao abrir o pagamento, focar no valor se já houver forma selecionada
                this.$nextTick(() => {
                    const inputValor = document.getElementById('input-valor-pagamento');
                    if (inputValor) inputValor.focus();
                });
            }
        }
    }"
    class="min-h-screen lg:h-screen lg:overflow-hidden bg-[#f1f5f9] dark:bg-[#0f172a] p-2 sm:p-4 font-sans antialiased">

    {{-- Cabeçalho Profissional --}}
    <div class="mb-4 flex justify-between items-center bg-white dark:bg-slate-900 p-4 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800">
        <div class="flex items-center gap-4">
            <div class="size-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-2xl shadow-lg shadow-indigo-600/20">
                🚀
            </div>
            <div>
                <h1 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight leading-none">
                    PDV Master
                </h1>
                <p class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 mt-1 uppercase tracking-widest">Atendimento Profissional</p>
            </div>
        </div>

        <div class="hidden lg:flex items-center gap-6">
            <div class="flex items-center gap-3 px-4 py-2 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700">
                <div class="text-right">
                    <p class="text-[9px] font-black text-slate-400 uppercase leading-none mb-1">Atalhos Rápidos</p>
                    <div class="flex gap-2">
                        <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300"><kbd class="bg-white dark:bg-slate-700 px-1.5 py-0.5 rounded shadow-sm mr-1">F2</kbd>Buscar</span>
                        <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300"><kbd class="bg-white dark:bg-slate-700 px-1.5 py-0.5 rounded shadow-sm mr-1">F8</kbd>Pagar</span>
                    </div>
                </div>
            </div>
            <div class="h-10 w-px bg-slate-200 dark:bg-slate-800"></div>
            <div class="text-right">
                <p class="text-[9px] font-black text-slate-400 uppercase leading-none mb-1">Data e Hora</p>
                <p class="text-sm font-black text-slate-700 dark:text-slate-200">{{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    {{-- Status do Caixa --}}
    @if($this->caixaAberto)
        <div class="mb-4 flex flex-wrap items-center gap-4 px-5 py-3 bg-emerald-50/50 dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-800/50 rounded-2xl">
            <div class="flex items-center gap-2">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                </span>
                <span class="font-black text-emerald-700 dark:text-emerald-400 text-xs uppercase tracking-widest">Caixa Operante</span>
            </div>
            <div class="h-4 w-px bg-emerald-200 dark:bg-emerald-800"></div>
            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-500">
                Operador: <span class="text-emerald-800 dark:text-emerald-300">{{ $this->caixaAberto->operador->name ?? 'N/A' }}</span>
            </span>
            <div class="ml-auto flex items-center gap-4">
                <div class="text-right">
                    <p class="text-[9px] font-black text-emerald-600/50 uppercase leading-none">Total em Vendas</p>
                    <p class="text-sm font-black text-emerald-700 dark:text-emerald-400">R$ {{ number_format($this->caixaAberto->total_vendas, 2, ',', '.') }}</p>
                </div>
            </div>
        </div>
    @else
        <div class="mb-4 flex items-center justify-between px-5 py-3 bg-rose-50 dark:bg-rose-900/10 border border-rose-100 dark:border-rose-800/50 rounded-2xl">
            <div class="flex items-center gap-3">
                <div class="size-8 bg-rose-500 rounded-full flex items-center justify-center text-white animate-pulse">⚠️</div>
                <span class="font-black text-rose-700 dark:text-rose-400 text-xs uppercase tracking-widest">Atenção: Caixa Fechado</span>
            </div>
            <a href="{{ route('tenant.caixa') }}"
                class="px-6 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg shadow-rose-600/20">
                Abrir Agora
            </a>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-4 lg:h-[calc(100vh-160px)] min-h-0">

        {{-- COLUNA ESQUERDA — Catálogo --}}
        <div class="xl:col-span-7 flex flex-col min-h-0 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            
            <div class="p-5 space-y-4 shrink-0 border-b border-slate-100 dark:border-slate-800">
                {{-- Barra de busca avançada --}}
                <div class="grid grid-cols-12 gap-3">
                    <div class="col-span-12 md:col-span-7 relative">
                        <input type="text" wire:model.live.debounce.300ms="busca" placeholder="Pesquisar produto ou categoria..."
                            class="w-full pl-12 pr-4 py-3.5 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-sm focus:ring-2 focus:ring-indigo-500 transition-all outline-none">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xl opacity-40">🔍</span>
                    </div>

                    <div class="col-span-8 md:col-span-3 relative">
                        <input type="text" wire:model="codigoProduto" wire:keydown.enter="buscarPorCodigo" placeholder="Cód/EAN" id="campo-codigo"
                            class="w-full pl-10 pr-4 py-3.5 bg-slate-50 dark:bg-slate-800 border-none rounded-2xl text-sm focus:ring-2 focus:ring-indigo-500 transition-all outline-none">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-lg opacity-40">🏷️</span>
                    </div>

                    <div class="col-span-4 md:col-span-2 relative">
                        <input type="text" placeholder="1.0" id="campo-quantidade"
                            x-data="{ raw: '' }"
                            x-on:keydown.enter="
                                let num = raw === '' ? 1 : parseInt(raw) / 1000;
                                $wire.set('quantidadeInput', num).then(() => {
                                    document.getElementById('campo-codigo').dispatchEvent(new KeyboardEvent('keydown', { key: 'Enter', bubbles: true }));
                                });
                            "
                            x-on:keydown="
                                if (event.key === 'Enter') return;
                                if (event.key === 'Backspace') { raw = raw.slice(0, -1); } 
                                else if (event.key >= '0' && event.key <= '9') { raw = raw + event.key; } 
                                else { return; }
                                event.preventDefault();
                                let num = raw === '' ? 0 : parseInt(raw) / 1000;
                                $el.value = num.toLocaleString('pt-BR', { minimumFractionDigits: 3, maximumFractionDigits: 3 });
                            "
                            x-on:focus="raw = ''; $el.value = '1,000'"
                            x-on:blur="let num = raw === '' ? 1 : parseInt(raw) / 1000; $wire.set('quantidadeInput', num); raw = '';"
                            class="w-full px-4 py-3.5 bg-indigo-50 dark:bg-indigo-900/20 border-none rounded-2xl text-sm font-black text-indigo-600 text-center focus:ring-2 focus:ring-indigo-500 transition-all outline-none">
                    </div>
                </div>

                {{-- Filtro de Categorias Moderno --}}
                <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-thin">
                    <button wire:click="selecionarCategoria(null)"
                        class="px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest whitespace-nowrap transition-all
                            {{ !$categoriaSelecionada ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                        📦 TODOS
                    </button>
                    @foreach($categorias as $categoria)
                        <button wire:click="selecionarCategoria({{ $categoria->id }})"
                            class="px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest whitespace-nowrap transition-all
                                {{ $categoriaSelecionada == $categoria->id ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                            {{ $categoria->icone ?? '📌' }} {{ $categoria->nome }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Grid de Produtos Estilo Galeria --}}
            <div class="flex-1 min-h-0 overflow-y-auto p-5 scrollbar-thin bg-slate-50/30 dark:bg-slate-900/30">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 content-start">
                    @forelse($produtos as $produto)
                        <button wire:click="adicionarProduto({{ $produto->id }})" wire:key="prod-{{ $produto->id }}"
                            class="group relative bg-white dark:bg-slate-800 p-4 rounded-[2rem] border border-slate-100 dark:border-slate-700 text-center hover:border-indigo-500 dark:hover:border-indigo-500 hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-300 active:scale-95 overflow-hidden">
                            <div class="mb-3 mx-auto size-16 bg-slate-50 dark:bg-slate-700 rounded-2xl flex items-center justify-center text-3xl group-hover:scale-110 group-hover:rotate-6 transition-transform duration-300">
                                {{ $produto->icone ?? '📦' }}
                            </div>
                            <h4 class="font-black text-[11px] uppercase leading-tight text-slate-800 dark:text-slate-100 line-clamp-2 min-h-[2.2rem]">
                                {{ $produto->nome }}
                            </h4>
                            <div class="mt-3 pt-3 border-t border-slate-50 dark:border-slate-700 flex flex-col items-center">
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Preço Unit.</span>
                                <span class="text-xs font-black text-indigo-600 dark:text-indigo-400">R$ {{ number_format($produto->preco_atual, 2, ',', '.') }}</span>
                            </div>
                            <div class="absolute top-2 right-2 size-6 bg-indigo-600 text-white rounded-full flex items-center justify-center text-xs font-black opacity-0 group-hover:opacity-100 transition-opacity">+</div>
                        </button>
                    @empty
                        <div class="col-span-full py-20 text-center opacity-20">
                            <span class="text-6xl">📦</span>
                            <p class="mt-4 font-black uppercase tracking-widest">Nenhum item disponível</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- COLUNA DIREITA — Carrinho --}}
        <div class="xl:col-span-5 flex flex-col min-h-0 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xl overflow-hidden">
            
            <div class="p-5 bg-slate-900 dark:bg-black text-white shrink-0">
                <div class="flex justify-between items-center mb-5">
                    <div class="flex items-center gap-3">
                        <div class="size-10 bg-indigo-600 rounded-xl flex items-center justify-center text-xl shadow-lg shadow-indigo-600/20">🛒</div>
                        <div>
                            <h3 class="font-black text-sm uppercase tracking-tighter">Resumo da Venda</h3>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ number_format($this->totalItens, 0) }} itens no total</p>
                        </div>
                    </div>
                    @if(count($carrinho) > 0)
                        <button wire:click="limparCarrinho" wire:confirm="Limpar carrinho?" class="px-3 py-1.5 bg-rose-600/10 text-rose-500 rounded-lg text-[9px] font-black uppercase hover:bg-rose-600 hover:text-white transition-all">Limpar</button>
                    @endif
                </div>
                
                <div class="grid grid-cols-2 gap-3">
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs opacity-50">📍</span>
                        <input type="text" wire:model.live.debounce.500ms="mesa" placeholder="Mesa / Comanda"
                            class="w-full pl-9 pr-3 py-2.5 bg-white/5 border-none rounded-xl text-xs text-white focus:ring-1 focus:ring-indigo-500 transition-all outline-none">
                    </div>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs opacity-50">👤</span>
                        <select wire:model="clienteId" class="w-full pl-9 pr-3 py-2.5 bg-white/5 border-none rounded-xl text-xs text-white focus:ring-1 focus:ring-indigo-500 transition-all outline-none">
                            <option value="" class="text-slate-900">Consumidor Padrão</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}" class="text-slate-900">{{ $cliente->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex-1 min-h-0 overflow-y-auto p-4 space-y-3 scrollbar-thin bg-slate-50/50 dark:bg-slate-900/50">
                @forelse($carrinho as $chave => $item)
                    <div class="flex items-center gap-4 p-4 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 group transition-all hover:border-indigo-200 dark:hover:border-indigo-900" wire:key="item-{{ $chave }}">
                        <div class="size-12 bg-slate-50 dark:bg-slate-700 rounded-xl flex items-center justify-center text-2xl shadow-inner group-hover:scale-110 transition-transform">{{ $item['icone'] ?? '📦' }}</div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start">
                                <h5 class="font-black text-xs uppercase truncate text-slate-800 dark:text-slate-200">{{ $item['nome'] }}</h5>
                                <button wire:click="removerProduto({{ $item['id'] }})" class="text-slate-300 hover:text-rose-500 transition-colors">✕</button>
                            </div>
                            <div class="flex justify-between items-center mt-2">
                                <div class="flex items-center gap-3 bg-slate-100 dark:bg-slate-900 px-3 py-1.5 rounded-xl shadow-inner">
                                    <button wire:click="atualizarQuantidade({{ $item['id'] }}, {{ $item['quantidade'] - 1 }})" class="text-slate-400 hover:text-indigo-600 transition-colors font-bold">➖</button>
                                    <span class="text-xs font-black min-w-[2rem] text-center">{{ number_format($item['quantidade'], $item['quantidade'] == intval($item['quantidade']) ? 0 : 3, ',', '.') }}</span>
                                    <button wire:click="atualizarQuantidade({{ $item['id'] }}, {{ $item['quantidade'] + 1 }})" class="text-slate-400 hover:text-indigo-600 transition-colors font-bold">➕</button>
                                </div>
                                <div class="text-right">
                                    <p class="text-[9px] font-bold text-slate-400 uppercase leading-none">{{ $item['preco_formatado'] }}</p>
                                    <p class="font-black text-sm text-indigo-600 dark:text-indigo-400">R$ {{ number_format($item['subtotal'], 2, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="h-full flex flex-col items-center justify-center py-20 opacity-20">
                        <div class="size-20 bg-slate-200 dark:bg-slate-800 rounded-full flex items-center justify-center text-4xl mb-4">🛒</div>
                        <p class="font-black uppercase tracking-widest text-xs">Carrinho Vazio</p>
                    </div>
                @endforelse
            </div>

            <div class="p-6 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 shrink-0 shadow-[0_-10px_20px_rgba(0,0,0,0.02)]">
                @php
                    $totalPago = round(collect($pagamentos)->sum('valor'), 2);
                    $pendente = max(0, $this->totalCarrinho - $totalPago);
                @endphp
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total da Venda</p>
                        <p class="text-4xl font-black text-slate-900 dark:text-white leading-none tracking-tighter">R$ {{ number_format($this->totalCarrinho, 2, ',', '.') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Status Pagamento</p>
                        <span class="px-3 py-1.5 {{ $pendente > 0 ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600' }} rounded-full text-[10px] font-black uppercase tracking-widest">
                            {{ $pendente > 0 ? 'Pendente' : 'Pago' }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-4">
                    @if($modoComanda && $mesa)
                        <button wire:click="salvarComanda" @if(empty($carrinho)) disabled @endif
                            class="py-4 border-2 border-indigo-600 text-indigo-600 dark:text-indigo-400 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-all disabled:opacity-30">
                            💾 Salvar Comanda
                        </button>
                    @endif
                    <button @click="showPayment = true; $nextTick(() => document.getElementById('input-valor-pagamento')?.focus())" @if(empty($carrinho)) disabled @endif
                        class="{{ ($modoComanda && $mesa) ? 'col-span-1' : 'col-span-2' }} py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-indigo-600/30 hover:scale-[1.02] transition-all active:scale-95 disabled:opacity-30">
                        💳 Finalizar Venda (F8)
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DE PAGAMENTO ESTILO MERCADO --}}
    <div x-show="showPayment" 
        x-trap.noscroll="showPayment" 
        x-transition:enter="transition ease-out duration-300" 
        x-transition:enter-start="opacity-0 scale-95" 
        x-transition:enter-end="opacity-100 scale-100" 
        x-transition:leave="transition ease-in duration-200" 
        x-transition:leave-start="opacity-100 scale-100" 
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/90 backdrop-blur-md" style="display: none;">
        
        <div @click.away="showPayment = false" class="bg-white dark:bg-slate-900 w-full max-w-5xl rounded-[2.5rem] shadow-2xl overflow-hidden border border-white/10 flex flex-col max-h-[90vh]">
            
            {{-- Header do Modal --}}
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-indigo-600 text-white">
                <div class="flex items-center gap-4">
                    <div class="size-12 bg-white/20 rounded-xl flex items-center justify-center text-2xl">💰</div>
                    <div>
                        <h3 class="font-black text-xl uppercase tracking-tighter">Pagamento da Venda</h3>
                        <p class="text-[10px] text-indigo-100 font-bold uppercase tracking-widest">Igual PDV de Mercado</p>
                    </div>
                </div>
                <button @click="showPayment = false" class="size-10 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center transition-colors">✕</button>
            </div>

            <div class="p-0 flex-1 overflow-hidden flex flex-col lg:flex-row">
                
                {{-- Lado Esquerdo: Seleção e Entrada --}}
                <div class="lg:w-2/3 p-8 border-r border-slate-100 dark:border-slate-800 space-y-8 overflow-y-auto">
                    
                    {{-- Valor Total Grande --}}
                    <div class="bg-slate-50 dark:bg-slate-800/50 p-6 rounded-3xl border border-slate-100 dark:border-slate-700 flex justify-between items-center">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total a Pagar</p>
                            <p class="text-4xl font-black text-slate-900 dark:text-white tracking-tighter">R$ {{ number_format($this->totalCarrinho, 2, ',', '.') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">Faltante</p>
                            <p class="text-3xl font-black text-indigo-600 tracking-tighter">R$ {{ number_format($pendente, 2, ',', '.') }}</p>
                        </div>
                    </div>

                    {{-- Seleção de Forma de Pagamento --}}
                    <div>
                        <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-4">1. Escolha a Forma de Pagamento</p>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @foreach([
                                ['dinheiro', 'Dinheiro', '💵'],
                                ['cartao_credito', 'Crédito', '💳'],
                                ['cartao_debito', 'Débito', '💳'],
                                ['pix', 'PIX', '📱']
                            ] as [$val, $label, $icon])
                            <button wire:click="$set('formaPagamento', '{{ $val }}')"
                                @click="$nextTick(() => document.getElementById('input-valor-pagamento').focus())"
                                class="p-6 rounded-2xl border-2 transition-all flex flex-col items-center gap-2
                                    {{ $formaPagamento === $val 
                                        ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 shadow-lg shadow-indigo-600/10' 
                                        : 'border-slate-50 dark:border-slate-800 hover:border-indigo-200 text-slate-400' }}">
                                <span class="text-3xl">{{ $icon }}</span>
                                <span class="text-[10px] font-black uppercase tracking-widest">{{ $label }}</span>
                            </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Entrada de Valor --}}
                    <div class="pt-8 border-t border-slate-100 dark:border-slate-800">
                        <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-4">2. Informe o Valor e Confirme</p>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <div class="flex-1 relative">
                                <span class="absolute left-5 top-1/2 -translate-y-1/2 font-black text-slate-400 text-xl">R$</span>
                                <input type="number" 
                                    id="input-valor-pagamento"
                                    wire:model.live="valorPagamento" 
                                    wire:keydown.enter="adicionarPagamento"
                                    step="0.01"
                                    placeholder="0,00"
                                    class="w-full pl-14 pr-6 py-6 bg-slate-100 dark:bg-slate-800 border-none rounded-3xl font-black text-3xl focus:ring-4 focus:ring-indigo-500/20 outline-none transition-all">
                            </div>
                            <button wire:click="adicionarPagamento" 
                                @if(empty($carrinho) || $valorPagamento <= 0) disabled @endif
                                class="px-10 py-6 bg-indigo-600 text-white rounded-3xl font-black text-xs uppercase tracking-widest shadow-xl shadow-indigo-600/30 hover:bg-indigo-700 active:scale-95 transition-all disabled:opacity-30">
                                Confirmar [Enter]
                            </button>
                        </div>
                        @if($valorPagamento > $pendente && $formaPagamento === 'dinheiro')
                            <p class="mt-3 text-center text-sm font-bold text-amber-600 bg-amber-50 dark:bg-amber-900/20 p-3 rounded-xl border border-amber-100 dark:border-amber-800">
                                💰 Troco Estimado: R$ {{ number_format($valorPagamento - $pendente, 2, ',', '.') }}
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Lado Direito: Resumo de Pagamentos --}}
                <div class="lg:w-1/3 bg-slate-50/50 dark:bg-black/20 p-8 flex flex-col">
                    <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-6">Pagamentos Inseridos</p>
                    
                    <div class="flex-1 space-y-3 overflow-y-auto pr-2 scrollbar-thin">
                        @forelse($pagamentos as $index => $pag)
                        <div class="flex items-center justify-between bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 animate-in">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">{{ $pag['forma'] === 'dinheiro' ? '💵' : ($pag['forma'] === 'pix' ? '📱' : '💳') }}</span>
                                <div>
                                    <p class="text-[10px] font-black uppercase text-slate-400 leading-none mb-1">{{ str_replace('_', ' ', $pag['forma']) }}</p>
                                    <p class="font-black text-slate-800 dark:text-slate-100">R$ {{ number_format($pag['valor'], 2, ',', '.') }}</p>
                                </div>
                            </div>
                            <button wire:click="removerPagamento({{ $index }})" class="size-8 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center hover:bg-rose-500 hover:text-white transition-colors">✕</button>
                        </div>
                        @empty
                        <div class="h-full flex flex-col items-center justify-center opacity-20 py-10">
                            <span class="text-5xl mb-4">💳</span>
                            <p class="text-[10px] font-black uppercase tracking-widest text-center">Aguardando<br>Pagamentos</p>
                        </div>
                        @endforelse
                    </div>

                    {{-- Resumo Financeiro Final --}}
                    <div class="mt-8 pt-8 border-t border-slate-200 dark:border-slate-700 space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Já Pago</span>
                            <span class="font-black text-emerald-600">R$ {{ number_format($totalPago, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-end">
                            <span class="text-xs font-black uppercase text-slate-800 dark:text-slate-200">Restante</span>
                            <span class="text-3xl font-black {{ $pendente > 0 ? 'text-rose-500' : 'text-emerald-500' }} tracking-tighter">
                                R$ {{ number_format($pendente, 2, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer com Botão de Finalização --}}
            <div class="p-8 bg-slate-50 dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800">
                <button wire:click="finalizarVenda" 
                    @if(empty($carrinho) || $pendente > 0) disabled @endif
                    class="w-full py-6 bg-emerald-600 hover:bg-emerald-700 text-white rounded-[2rem] font-black text-lg uppercase tracking-widest shadow-2xl shadow-emerald-600/30 hover:scale-[1.01] active:scale-95 transition-all disabled:opacity-20 disabled:cursor-not-allowed flex items-center justify-center gap-3">
                    @if($pendente > 0)
                        <span>Aguardando Pagamento Total...</span>
                    @else
                        <span>✅ FINALIZAR VENDA E IMPRIMIR</span>
                    @endif
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL PARA NF NA FINALIZAÇÃO — CÓPIA EXATA DO CÓDIGO FUNCIONAL --}}
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
            .scrollbar-thin::-webkit-scrollbar { width: 5px; height: 5px; }
            .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
            .scrollbar-thin::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
            .dark .scrollbar-thin::-webkit-scrollbar-thumb { background: #334155; }
            
            @keyframes zoom-in { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
            .animate-in { animation: zoom-in 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
            
            .custom-scrollbar::-webkit-scrollbar { width: 4px; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        </style>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                setTimeout(() => document.getElementById('campo-codigo')?.focus(), 100);
                document.addEventListener('focar-codigo', () => document.getElementById('campo-codigo')?.focus());
            });
        </script>
    @endpush
</div>