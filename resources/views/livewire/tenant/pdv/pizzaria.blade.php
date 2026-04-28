<div x-data="{ showPayment: false }" class="min-h-screen lg:h-screen lg:overflow-hidden bg-gradient-to-br from-amber-50 to-orange-50 p-2 sm:p-3">

    {{-- Cabeçalho --}}
    <div class="mb-3 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold bg-gradient-to-r from-red-700 to-orange-600 bg-clip-text text-transparent">🍕 Pizzaria</h1>
            <p class="text-xs text-ink-500 mt-0.5">Monte sua pizza | F2 código | F8 pagar</p>
        </div>
        <div class="flex items-center gap-2 text-xs bg-ink-100 px-3 py-1.5 rounded-full">
            <span class="font-mono">F2</span> Código | <span class="font-mono">F5</span> Finalizar | <span class="font-mono">F6</span> Novo | <span class="font-mono">F8</span> Pagar
        </div>
    </div>

    {{-- Status do Caixa --}}
    @if($this->caixaAberto)
        <div class="mb-3 flex items-center gap-3 px-3 py-2 bg-green-50 rounded-xl border border-green-200">
            <span class="size-2 rounded-full bg-green-500 animate-pulse"></span>
            <span class="font-semibold text-green-700">💰 Caixa aberto</span>
            <span class="text-xs text-green-600">{{ $this->caixaAberto->aberto_em->format('d/m H:i') }}</span>
            <span class="text-xs font-bold text-green-700">R$ {{ number_format($this->caixaAberto->total_vendas, 2, ',', '.') }}</span>
        </div>
    @else
        <div class="mb-3 flex items-center justify-between px-3 py-2 bg-red-50 rounded-xl border border-red-200">
            <span class="font-semibold text-red-700">⚠️ Nenhum caixa aberto</span>
            <a href="{{ route('tenant.caixa') }}" class="px-3 py-1 bg-red-600 text-white rounded-lg text-xs">Abrir Caixa</a>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 lg:h-[calc(100vh-135px)] min-h-0">

        {{-- ÁREA DE MONTAGEM DA PIZZA --}}
        <div class="lg:col-span-8 bg-white rounded-xl border shadow-sm overflow-hidden flex flex-col min-h-0">
            
            {{-- Busca rápida --}}
            <div class="p-3 border-b">
                <div class="grid grid-cols-12 gap-2">
                    <div class="col-span-12 sm:col-span-8 relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-ink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                        </svg>
                        <input type="text" wire:model.live.debounce.300ms="busca" placeholder="Buscar pizza..." class="w-full pl-9 pr-3 py-2 border rounded-lg text-sm">
                    </div>
                    <div class="col-span-12 sm:col-span-4 relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-ink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M4 6h1m1 0h1M4 12h1m1 0h1M4 18h1m1 0h1M15 6h1m1 0h1M15 12h1m1 0h1M15 18h1m1 0h1M9 3v18M12 3v18"/>
                        </svg>
                        <input type="text" wire:model="codigoProduto" wire:keydown.enter="buscarPorCodigo" placeholder="Código" id="campo-codigo" class="w-full pl-9 pr-3 py-2 border rounded-lg text-sm">
                    </div>
                </div>
            </div>

            {{-- Botão para montar pizza --}}
            <div class="p-6 text-center">
                <button wire:click="abrirModalPizza()" class="px-8 py-4 bg-gradient-to-r from-red-600 to-orange-600 text-white rounded-2xl font-bold text-xl shadow-lg hover:scale-105 transition-all">
                    🍕 Montar Pizza
                </button>
                <p class="text-xs text-ink-500 mt-3">Clique para montar sua pizza do jeito que quiser</p>
            </div>

            {{-- Pizzas rápidas (se houver) --}}
            @if(count($pizzas) > 0)
            <div class="p-3 border-t">
                <p class="text-xs font-semibold text-ink-500 mb-2">🍕 Pizzas do cardápio</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @foreach($pizzas as $pizza)
                        <button wire:click="abrirModalPizza({{ $pizza }})" class="bg-gray-50 border rounded-lg p-2 text-center hover:shadow-md">
                            <span class="text-2xl">🍕</span>
                            <p class="text-xs font-semibold">{{ $pizza->nome }}</p>
                            <p class="text-sm font-bold text-primary-600">R$ {{ number_format($pizza->preco_atual, 2, ',', '.') }}</p>
                        </button>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- CARRINHO --}}
        <div class="lg:col-span-4 bg-white rounded-xl border shadow-sm overflow-hidden flex flex-col min-h-0">
            <div class="p-3 border-b bg-gray-50 flex justify-between items-center">
                <span class="font-bold">🛒 Carrinho ({{ number_format($this->totalItens, 0) }})</span>
                @if(count($carrinho) > 0)
                    <button wire:click="limparCarrinho" class="text-xs text-red-500">Limpar</button>
                @endif
            </div>

            <div class="flex-1 overflow-y-auto divide-y">
                @forelse($carrinho as $index => $item)
                    <div class="p-3">
                        <div class="flex justify-between">
                            <div>
                                <p class="font-medium text-sm">{{ $item['nome'] }}</p>
                                @if(isset($item['observacao']))
                                    <p class="text-[10px] text-ink-400">{{ $item['observacao'] }}</p>
                                @endif
                            </div>
                            <button wire:click="removerProduto({{ $index }})" class="text-red-400">✕</button>
                        </div>
                        <div class="flex justify-between items-center mt-2">
                            <div class="flex items-center gap-1">
                                <button wire:click="atualizarQuantidade({{ $index }}, {{ $item['quantidade'] - 0.5 }})" class="size-6 rounded bg-gray-100">−</button>
                                <span class="w-12 text-center text-sm">{{ number_format($item['quantidade'], 0) }} un</span>
                                <button wire:click="atualizarQuantidade({{ $index }}, {{ $item['quantidade'] + 0.5 }})" class="size-6 rounded bg-gray-100">+</button>
                            </div>
                            <p class="font-bold text-sm">R$ {{ number_format($item['subtotal'], 2, ',', '.') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center text-ink-400">
                        <p>Carrinho vazio</p>
                        <p class="text-xs mt-1">Monte uma pizza para começar</p>
                    </div>
                @endforelse
            </div>

            <div class="p-3 border-t bg-gray-50">
                <div class="flex justify-between text-xl font-bold mb-3">
                    <span>Total:</span>
                    <span class="text-primary-600">R$ {{ number_format($this->totalCarrinho, 2, ',', '.') }}</span>
                </div>
                <button @click="showPayment = true" @if(empty($carrinho)) disabled @endif class="w-full py-3 bg-green-600 text-white rounded-lg font-bold">💳 Finalizar Venda</button>
            </div>
        </div>
    </div>

    {{-- MODAL PARA MONTAR PIZZA --}}
    @if($mostrarModalPizza && $produtoSelecionado)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60">
        <div class="bg-white rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto m-4">
            <div class="sticky top-0 bg-white p-4 border-b flex justify-between items-center">
                <h2 class="text-xl font-bold flex items-center gap-2">
                    <span class="text-2xl">🍕</span> Montar Pizza
                </h2>
                <button wire:click="$set('mostrarModalPizza', false)" class="p-2 hover:bg-gray-100 rounded-full">✕</button>
            </div>
            
            <div class="p-4 space-y-4">
                {{-- Tamanho --}}
                <div>
                    <label class="block text-sm font-semibold mb-2">Tamanho</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($tamanhosDisponiveis as $tam)
                            <button wire:click="$set('tamanhoSelecionado', '{{ $tam }}')" 
                                class="p-3 border-2 rounded-xl text-center transition-all {{ $tamanhoSelecionado == $tam ? 'border-red-600 bg-red-50' : 'border-gray-200' }}">
                                {{ $tam }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Meia pizza --}}
                <div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="meiaPizza" class="size-4">
                        <span class="text-sm font-semibold">🍕 Meia Pizza (metade do preço)</span>
                    </label>
                </div>

                {{-- Sabores --}}
                <div>
                    <label class="block text-sm font-semibold mb-2">{{ $meiaPizza ? 'Primeiro Sabor' : 'Sabor' }}</label>
                    <select wire:model="sabor1" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">Selecione um sabor</option>
                        @foreach($saboresDisponiveis as $sabor)
                            <option value="{{ $sabor->nome }}">{{ $sabor->nome }} - R$ {{ number_format($sabor->preco_atual, 2, ',', '.') }}</option>
                        @endforeach
                    </select>
                </div>

                @if($meiaPizza)
                <div>
                    <label class="block text-sm font-semibold mb-2">Segundo Sabor</label>
                    <select wire:model="sabor2" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">Selecione um sabor</option>
                        @foreach($saboresDisponiveis as $sabor)
                            <option value="{{ $sabor->nome }}">{{ $sabor->nome }} - R$ {{ number_format($sabor->preco_atual, 2, ',', '.') }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Borda --}}
                <div>
                    <label class="block text-sm font-semibold mb-2">Borda Recheada</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($bordasDisponiveis as $borda)
                            <button wire:click="$set('bordaSelecionada', '{{ $borda }}')" 
                                class="p-2 border-2 rounded-xl text-center text-sm transition-all {{ $bordaSelecionada == $borda ? 'border-red-600 bg-red-50' : 'border-gray-200' }}">
                                {{ $borda }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Adicionais --}}
                <div>
                    <label class="block text-sm font-semibold mb-2">Adicionais</label>
                    <div class="space-y-1">
                        @foreach($adicionaisDisponiveis as $adicional)
                            <label class="flex items-center gap-2 p-2 border rounded-lg cursor-pointer">
                                <input type="checkbox" wire:model="adicionais" value="{{ $adicional['nome'] }}">
                                <span>{{ $adicional['nome'] }}</span>
                                <span class="text-primary-600 ml-auto">+ R$ {{ number_format($adicional['preco'], 2, ',', '.') }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Quantidade --}}
                <div>
                    <label class="block text-sm font-semibold mb-2">Quantidade</label>
                    <div class="flex items-center gap-2">
                        <button wire:click="$set('quantidade', max(1, $quantidade - 1))" class="size-8 rounded bg-gray-100">−</button>
                        <span class="w-16 text-center font-bold">{{ $quantidade }}</span>
                        <button wire:click="$set('quantidade', $quantidade + 1)" class="size-8 rounded bg-gray-100">+</button>
                    </div>
                </div>

                {{-- Valor --}}
                <div class="bg-gray-50 rounded-xl p-4">
                    <div class="flex justify-between font-bold">
                        <span>Valor estimado:</span>
                        <span class="text-primary-600 text-xl">R$ {{ number_format($this->calcularPrecoPizza(), 2, ',', '.') }}</span>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button wire:click="$set('mostrarModalPizza', false)" class="flex-1 py-3 border rounded-xl">Cancelar</button>
                    <button wire:click="adicionarPizza" class="flex-1 py-3 bg-red-600 text-white rounded-xl font-bold">Adicionar ao Carrinho</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- MODAL PAGAMENTO (mesmo da sorveteria) --}}
    <div x-show="showPayment" x-trap.noscroll="showPayment" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60" style="display: none;">
        <div @click.away="showPayment = false" class="bg-white rounded-2xl w-full max-w-2xl overflow-hidden">
            <div class="p-4 bg-gradient-to-r from-primary-50 to-indigo-50 flex justify-between items-center">
                <h3 class="font-bold">💳 Finalizar Pagamento</h3>
                <button @click="showPayment = false" class="p-2 hover:bg-gray-200 rounded-full">✕</button>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs font-semibold mb-2">Formas de Pagamento</p>
                        <div class="grid grid-cols-2 gap-2 mb-4">
                            @foreach([['dinheiro','💰','Dinheiro'],['cartao_credito','💳','Crédito'],['cartao_debito','💳','Débito'],['pix','📱','PIX']] as [$val,$icon,$label])
                                <button wire:click="$set('formaPagamento', '{{ $val }}')"
                                    class="p-3 rounded-xl border-2 {{ $formaPagamento === $val ? 'border-primary-600 bg-primary-50' : 'border-gray-200' }}">
                                    <span class="text-2xl">{{ $icon }}</span>
                                    <p class="text-xs">{{ $label }}</p>
                                </button>
                            @endforeach
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" wire:model.live="valorPagamento" step="0.01" placeholder="Valor" class="px-3 py-2 border rounded-xl">
                            <button wire:click="adicionarPagamento" class="px-3 py-2 bg-amber-500 text-white rounded-xl">Adicionar</button>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-semibold mb-2">Pagamentos</p>
                        <div class="space-y-2 max-h-64 overflow-y-auto mb-4">
                            @forelse($pagamentos as $index => $pag)
                                <div class="flex justify-between bg-green-50 p-2 rounded-lg">
                                    <span>{{ $pag['forma'] === 'dinheiro' ? '💰' : '💳' }} {{ ucfirst(str_replace('_',' ',$pag['forma'])) }}</span>
                                    <span>R$ {{ number_format($pag['valor'],2,',','.') }}</span>
                                    <button wire:click="removerPagamento({{ $index }})" class="text-red-500">✕</button>
                                </div>
                            @empty
                                <p class="text-center py-4 text-ink-400">Nenhum pagamento</p>
                            @endforelse
                        </div>
                        @php $totalPago = round(collect($pagamentos)->sum('valor'), 2); $pendente = max(0, $this->totalCarrinho - $totalPago); @endphp
                        <div class="border-t pt-3">
                            <div class="flex justify-between font-bold"><span>Total:</span><span>R$ {{ number_format($this->totalCarrinho,2,',','.') }}</span></div>
                            <div class="flex justify-between text-sm mt-1"><span>Pago:</span><span class="text-green-600">R$ {{ number_format($totalPago,2,',','.') }}</span></div>
                            <div class="flex justify-between text-sm"><span>Restante:</span><span class="{{ $pendente > 0 ? 'text-red-600' : 'text-green-600' }}">R$ {{ number_format($pendente,2,',','.') }}</span></div>
                        </div>
                    </div>
                </div>
                <div class="mt-6">
                    <button wire:click="finalizarVenda" @if($pendente > 0) disabled @endif class="w-full py-3 bg-green-600 text-white rounded-xl font-bold disabled:opacity-40">✅ FINALIZAR VENDA</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('keydown', function(e) {
            if (e.key === 'F2') { e.preventDefault(); document.getElementById('campo-codigo')?.focus(); }
            if (e.key === 'F5') { e.preventDefault(); const wire = Livewire.find(document.querySelector('[wire\\:id]')?.getAttribute('wire:id')); if(wire) wire.call('finalizarVenda'); }
            if (e.key === 'F6') { e.preventDefault(); if(confirm('Nova venda?')) { const wire = Livewire.find(document.querySelector('[wire\\:id]')?.getAttribute('wire:id')); if(wire) wire.call('limparCarrinho'); } }
            if (e.key === 'F8') { e.preventDefault(); document.querySelector('[x-data]')?.__x?.$data?.showPayment = true; }
        });
        setTimeout(() => document.getElementById('campo-codigo')?.focus(), 100);
    </script>
    @endpush
</div>