<div
    x-data="{}"
    x-on:pdv-sucesso.window="alert($event.detail.mensagem)"
    x-on:pdv-aviso.window="alert($event.detail.mensagem)"
>
    {{-- Cabeçalho --}}
    <div class="mb-3 flex justify-between items-center">
        <div>
            <h1 class="text-xl font-semibold text-ink-900 dark:text-ink-50">Ponto de Venda</h1>
        </div>
        <div class="text-right">
            <p class="text-xs text-ink-500">F2 (Código) | F5 (Finalizar) | F6 (Novo)</p>
        </div>
    </div>

    {{-- Status do Caixa --}}
    @if($this->caixaAberto)
    <div class="mb-3 flex items-center gap-2 px-3 py-1.5 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-xs">
        <span class="size-2 rounded-full bg-green-500 animate-pulse inline-block flex-shrink-0"></span>
        <span class="font-medium text-green-700 dark:text-green-400">Caixa aberto</span>
        <span class="text-green-600">•</span>
        <span class="text-green-600">{{ $this->caixaAberto->aberto_em->format('d/m H:i') }}</span>
        <span class="text-green-600">•</span>
        <span class="text-green-600">{{ $this->caixaAberto->operador->name ?? 'N/A' }}</span>
        <span class="text-green-600">•</span>
        <span class="text-green-600">{{ $this->caixaAberto->quantidade_vendas }} vendas</span>
        <span class="text-green-600">•</span>
        <span class="font-medium text-green-700 dark:text-green-400">R$ {{ number_format($this->caixaAberto->total_vendas, 2, ',', '.') }}</span>
    </div>
    @else
    <div class="mb-3 flex items-center justify-between px-3 py-1.5 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-xs">
        <div class="flex items-center gap-2">
            <span class="size-2 rounded-full bg-red-500 inline-block"></span>
            <span class="font-medium text-red-700 dark:text-red-400">Nenhum caixa aberto</span>
        </div>
        <a href="{{ route('tenant.caixa') }}" class="px-2 py-1 bg-red-600 text-white rounded text-xs font-medium hover:bg-red-700 transition-colors">
            Abrir Caixa
        </a>
    </div>
    @endif

    {{-- Layout principal: 2 colunas --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">

        {{-- COLUNA ESQUERDA — Produtos --}}
        <div class="lg:col-span-3 space-y-3">

            {{-- Busca + Código + Quantidade --}}
            <div class="grid grid-cols-12 gap-2">
                <div class="col-span-5 relative">
                    <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 size-4 text-ink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                    </svg>
                    <input type="text" wire:model.live.debounce.300ms="busca" placeholder="Buscar produto..."
                        class="w-full pl-8 pr-2 py-1.5 border border-gray-300 rounded-lg text-sm dark:bg-ink-800 dark:border-ink-600 dark:text-ink-100 focus:outline-none focus:ring-2 focus:ring-ink-500">
                </div>
                <div class="col-span-4 relative">
                    <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 size-4 text-ink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h1m1 0h1M4 12h1m1 0h1M4 18h1m1 0h1M15 6h1m1 0h1M15 12h1m1 0h1M15 18h1m1 0h1M9 3v18M12 3v18"/>
                    </svg>
                    <input type="text" wire:model="codigoProduto" wire:keydown.enter="buscarPorCodigo"
                        placeholder="Código / ID" id="campo-codigo" autofocus
                        class="w-full pl-8 pr-2 py-1.5 border border-gray-300 rounded-lg text-sm dark:bg-ink-800 dark:border-ink-600 dark:text-ink-100 focus:outline-none focus:ring-2 focus:ring-ink-500">
                </div>
                <div class="col-span-3 relative">
                    <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 size-4 text-ink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                    </svg>
                    <input type="text" placeholder="1,000" id="campo-quantidade"
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
                        x-on:blur="
                            let num = raw === '' ? 1 : parseInt(raw) / 1000;
                            $wire.set('quantidadeInput', num);
                            raw = '';
                        "
                        class="w-full pl-8 pr-2 py-1.5 border border-gray-300 rounded-lg text-sm dark:bg-ink-800 dark:border-ink-600 dark:text-ink-100 focus:outline-none focus:ring-2 focus:ring-ink-500">
                </div>
            </div>

            {{-- Categorias --}}
            <div class="flex gap-1.5 overflow-x-auto pb-1">
                <button wire:click="selecionarCategoria(null)"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium whitespace-nowrap transition-colors
                           {{ !$categoriaSelecionada ? 'bg-ink-900 text-white dark:bg-ink-100 dark:text-ink-900' : 'bg-ink-100 text-ink-700 hover:bg-ink-200 dark:bg-ink-800 dark:text-ink-300' }}">
                    TODOS
                </button>
                @foreach($categorias as $categoria)
                <button wire:click="selecionarCategoria({{ $categoria->id }})"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium whitespace-nowrap transition-colors
                           {{ $categoriaSelecionada == $categoria->id ? 'bg-ink-900 text-white dark:bg-ink-100 dark:text-ink-900' : 'bg-ink-100 text-ink-700 hover:bg-ink-200 dark:bg-ink-800 dark:text-ink-300' }}">
                    {{ $categoria->nome }}
                </button>
                @endforeach
            </div>

            {{-- Grid de Produtos --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-2 max-h-[calc(100vh-280px)] overflow-y-auto pr-1">
                @forelse($produtos as $produto)
                <button wire:click="adicionarProduto({{ $produto->id }})" wire:key="prod-{{ $produto->id }}"
                    class="bg-white dark:bg-ink-900 border border-gray-200 dark:border-ink-700 rounded-lg p-2.5 text-left hover:border-ink-400 hover:shadow-md transition-all active:scale-95">
                    <div class="w-full h-14 bg-ink-100 dark:bg-ink-800 rounded flex items-center justify-center mb-2">
                        <svg class="size-6 text-ink-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7H4a1 1 0 00-1 1v10a1 1 0 001 1h16a1 1 0 001-1V8a1 1 0 00-1-1zM16 3H8l-1 4h10l-1-4z"/>
                        </svg>
                    </div>
                    <p class="font-medium text-xs line-clamp-2 text-ink-800 dark:text-ink-100 leading-tight mb-1">{{ $produto->nome }}</p>
                    <p class="text-sm font-bold text-primary-600">R$ {{ number_format($produto->preco_atual, 2, ',', '.') }}</p>
                    @if($produto->codigo)
                    <p class="text-xs text-ink-400 font-mono mt-0.5">{{ $produto->codigo }}</p>
                    @endif
                </button>
                @empty
                <div class="col-span-full text-center py-12 text-ink-400">
                    <p class="text-sm">Nenhum produto encontrado</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- COLUNA DIREITA — Carrinho + Pagamento --}}
        <div class="lg:col-span-2 flex flex-col gap-3">

            {{-- Carrinho --}}
            <div class="bg-white dark:bg-ink-900 rounded-lg border border-gray-200 dark:border-ink-700">
                <div class="px-3 py-2 border-b border-gray-200 dark:border-ink-700">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-semibold text-sm text-ink-900 dark:text-ink-50">
                            Carrinho
                            <span class="font-normal text-ink-400 text-xs">({{ number_format($this->totalItens, 0) }} itens)</span>
                        </span>
                        @if(count($carrinho) > 0)
                        <button wire:click="limparCarrinho" wire:confirm="Limpar carrinho?"
                            class="text-xs text-red-500 hover:text-red-700">Limpar</button>
                        @endif
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-xs text-ink-500">Mesa/Comanda</label>
                            <input type="text" wire:model.live.debounce.500ms="mesa"
                                class="w-full mt-0.5 px-2 py-1 text-sm border border-gray-300 rounded-lg dark:bg-ink-800 dark:border-ink-600 dark:text-ink-100"
                                placeholder="Nº mesa">
                        </div>
                        <div>
                            <label class="text-xs text-ink-500">Cliente</label>
                            <select wire:model="clienteId"
                                class="w-full mt-0.5 px-2 py-1 text-sm border border-gray-300 rounded-lg dark:bg-ink-800 dark:border-ink-600 dark:text-ink-100">
                                <option value="">Consumidor</option>
                                @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="max-h-48 overflow-y-auto divide-y divide-gray-100 dark:divide-ink-700">
                    @forelse($carrinho as $chave => $item)
                    <div class="px-3 py-2" wire:key="item-{{ $chave }}">
                        <div class="flex justify-between items-start mb-1">
                            <div class="flex-1 min-w-0 pr-2">
                                <p class="text-xs font-medium text-ink-900 dark:text-ink-100 truncate">{{ $item['nome'] }}</p>
                                <p class="text-xs text-ink-400">{{ $item['preco_formatado'] }}</p>
                            </div>
                            <button wire:click="removerProduto({{ $item['id'] }})" class="text-red-400 hover:text-red-600">
                                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m-7 0a1 1 0 01-1-1V5h10v1a1 1 0 01-1 1H9z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-1">
                                <button wire:click="atualizarQuantidade({{ $item['id'] }}, {{ $item['quantidade'] - 1 }})"
                                    class="size-5 rounded bg-ink-100 hover:bg-ink-200 dark:bg-ink-700 text-ink-700 dark:text-ink-100 text-sm leading-none flex items-center justify-center">−</button>
                                <span class="w-10 text-center text-xs font-medium text-ink-800 dark:text-ink-100">
                                    {{ number_format($item['quantidade'], $item['quantidade'] == intval($item['quantidade']) ? 0 : 3, ',', '.') }}
                                </span>
                                <button wire:click="atualizarQuantidade({{ $item['id'] }}, {{ $item['quantidade'] + 1 }})"
                                    class="size-5 rounded bg-ink-100 hover:bg-ink-200 dark:bg-ink-700 text-ink-700 dark:text-ink-100 text-sm leading-none flex items-center justify-center">+</button>
                            </div>
                            <p class="text-xs font-semibold text-ink-900 dark:text-ink-100">
                                R$ {{ number_format($item['subtotal'], 2, ',', '.') }}
                            </p>
                        </div>
                    </div>
                    @empty
                    <div class="py-6 text-center text-ink-400">
                        <p class="text-xs">Carrinho vazio</p>
                    </div>
                    @endforelse
                </div>

                <div class="px-3 py-2 border-t border-gray-200 dark:border-ink-700 bg-ink-50 dark:bg-ink-800/50 rounded-b-lg">
                    @if($modoComanda && $comandaId)
                        @php $comandaAtual = \App\Models\Tenant\Comanda::find($comandaId); @endphp
                        @if($comandaAtual && $comandaAtual->total_pago > 0)
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-ink-500">Já pago</span>
                            <span class="text-green-600 font-medium">R$ {{ number_format($comandaAtual->total_pago, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-ink-500">Restante</span>
                            <span class="text-red-600 font-medium">R$ {{ number_format($comandaAtual->total_restante, 2, ',', '.') }}</span>
                        </div>
                        @endif
                    @endif
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-sm text-ink-900 dark:text-ink-50">Total</span>
                        <span class="font-bold text-base text-primary-600">
                            R$ {{ number_format($this->totalCarrinho, 2, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- PAINEL DE PAGAMENTO --}}
            <div class="bg-white dark:bg-ink-900 rounded-lg border border-gray-200 dark:border-ink-700 p-3 space-y-3">

                {{-- Formas de pagamento --}}
                <div class="grid grid-cols-4 gap-1.5">
                    @foreach([
                        ['dinheiro',       'Dinheiro', 'M12 8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3zm0-6C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z'],
                        ['cartao_credito', 'Crédito',  'M20 4H4c-1.11 0-2 .89-2 2v12c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z'],
                        ['cartao_debito',  'Débito',   'M20 4H4c-1.11 0-2 .89-2 2v12c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z'],
                        ['pix',            'PIX',      'M9.5 6.5v3h-3v-3h3M11 5H5v6h6V5zm-1.5 9.5v3h-3v-3h3M11 13H5v6h6v-6zm6.5-6.5v3h-3v-3h3M19 5h-6v6h6V5zm-6 8h1.5v1.5H13V13zm1.5 1.5H16V16h-1.5v-1.5zM16 13h1.5v1.5H16V13zm-3 3h1.5v1.5H13V16zm1.5 1.5H16V19h-1.5v-1.5zM16 16h1.5v1.5H16V16zm1.5-1.5H19V16h-1.5v-1.5zm0 3H19V19h-1.5v-1.5zM22 7h-2V4h-3V2h5v5zm0 15v-5h-2v3h-3v2h5zM2 22h5v-2H4v-3H2v5zM2 2v5h2V4h3V2H2z'],
                    ] as [$val, $label, $path])
                    <button wire:click="$set('formaPagamento', '{{ $val }}')"
                        class="py-2 border rounded-lg text-center transition-colors text-xs font-medium
                               {{ $formaPagamento === $val
                                    ? 'bg-ink-900 text-white border-ink-900 dark:bg-ink-100 dark:text-ink-900'
                                    : 'bg-white dark:bg-ink-800 text-ink-700 dark:text-ink-200 border-gray-200 dark:border-ink-600 hover:bg-ink-50' }}">
                        <svg class="size-4 mx-auto mb-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="{{ $path }}"/></svg>
                        {{ $label }}
                    </button>
                    @endforeach
                </div>

                {{-- Valor + Troco --}}
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="text-xs text-ink-500 mb-0.5 block">Valor recebido</label>
                        <input type="number" wire:model.live="valorPagamento" step="0.01" min="0"
                            placeholder="0,00"
                            class="w-full px-2 py-1.5 border border-gray-300 dark:border-ink-600 rounded-lg text-sm font-semibold dark:bg-ink-800 dark:text-ink-100 focus:outline-none focus:ring-2 focus:ring-ink-500">
                    </div>
                    <div>
                        <label class="text-xs text-ink-500 mb-0.5 block">
                            {{ $formaPagamento === 'dinheiro' ? 'Troco' : 'Pendente' }}
                        </label>
                        <div class="w-full px-2 py-1.5 border border-gray-200 dark:border-ink-700 rounded-lg bg-ink-50 dark:bg-ink-800 text-sm font-semibold
                            {{ $formaPagamento === 'dinheiro' && $valorPagamento > $valorPendente && $valorPagamento > 0
                                ? 'text-amber-600'
                                : ($valorPendente > 0 ? 'text-red-600' : 'text-green-600') }}">
                            @if($formaPagamento === 'dinheiro' && $valorPagamento > $valorPendente && $valorPagamento > 0)
                                R$ {{ number_format($valorPagamento - $valorPendente, 2, ',', '.') }}
                            @else
                                R$ {{ number_format($valorPendente, 2, ',', '.') }}
                            @endif
                        </div>
                    </div>
                </div>
                @php
    $totalPago = round(collect($pagamentos)->sum('valor'), 2);
@endphp

<div class="rounded-lg border border-gray-200 dark:border-ink-700 p-2 space-y-1">
    <div class="flex justify-between text-xs">
        <span class="text-ink-500">Subtotal</span>
        <span class="font-medium text-ink-900 dark:text-ink-100">
            R$ {{ number_format($this->totalCarrinho, 2, ',', '.') }}
        </span>
    </div>

    <div class="flex justify-between text-xs">
        <span class="text-ink-500">Pago</span>
        <span class="font-medium text-green-600">
            R$ {{ number_format($totalPago, 2, ',', '.') }}
        </span>
    </div>

    <div class="flex justify-between text-xs">
        <span class="text-ink-500">Restante</span>
        <span class="font-semibold {{ $valorPendente > 0 ? 'text-red-600' : 'text-green-600' }}">
            R$ {{ number_format($valorPendente, 2, ',', '.') }}
        </span>
    </div>
</div>

                {{-- Pagamentos já lançados --}}
                @if(count($pagamentos) > 0)
                <div class="space-y-1">
                    @foreach($pagamentos as $index => $pag)
                    <div class="flex items-center justify-between bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded px-2 py-1">
                        <span class="text-xs text-ink-700 dark:text-ink-200 capitalize">{{ str_replace('_', ' ', $pag['forma']) }}</span>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-semibold text-green-700 dark:text-green-400">
                                R$ {{ number_format($pag['valor'], 2, ',', '.') }}
                            </span>
                            @if(isset($pag['troco']) && $pag['troco'] > 0)
                            <span class="text-xs text-amber-600">troco R$ {{ number_format($pag['troco'], 2, ',', '.') }}</span>
                            @endif
                            <button wire:click="removerPagamento({{ $index }})" class="text-red-400 hover:text-red-600 text-base leading-none">×</button>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Botões --}}
                {{-- Botões --}}
<div class="{{ $modoComanda ? 'grid grid-cols-2 gap-2' : 'space-y-2' }}">
    @if($modoComanda)
    <button wire:click="salvarComanda" @if(empty($carrinho)) disabled @endif
        class="py-2.5 border border-ink-900 dark:border-ink-100 text-ink-900 dark:text-ink-100 rounded-lg font-medium text-sm hover:bg-ink-50 dark:hover:bg-ink-800 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
        Salvar Mesa
    </button>
    @endif

    <button wire:click="adicionarPagamento" @if(empty($carrinho) || $valorPagamento <= 0) disabled @endif
        class="{{ $modoComanda ? '' : 'w-full' }} py-2.5 bg-amber-500 text-white rounded-lg font-medium text-sm hover:bg-amber-600 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
        + Adicionar Pagamento
    </button>

    @unless($modoComanda)
    <button wire:click="finalizarVenda" @if(empty($carrinho) || $valorPendente > 0) disabled @endif
        class="w-full py-2.5 bg-green-600 text-white rounded-lg font-medium text-sm hover:bg-green-700 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
        Finalizar Venda (F5)
    </button>
    @endunless
</div>
            </div>
        </div>
    </div>

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
                    Livewire.find(document.querySelector('[wire\\:id]')?.getAttribute('wire:id'))?.call('acaoF5');
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
