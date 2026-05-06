<div
    x-data="atendimentoRapido()"
    x-init="init()"
    class="min-h-screen bg-slate-50 dark:bg-ink-950 p-3">

    <div class="mb-5 flex items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-ink-900 dark:text-ink-50">
                Atendimento Rápido
            </h1>
            <p class="text-sm text-ink-500 dark:text-ink-400">
                Crie pedidos de entrega, retirada ou consumo local com poucos cliques.
            </p>
        </div>

        <button type="button"
            wire:click="limparTela"
            class="px-4 py-2 rounded-xl border border-gray-300 dark:border-ink-700 text-sm font-semibold hover:bg-white dark:hover:bg-ink-900">
            Nova venda
        </button>
    </div>

    {{-- Tipo do pedido --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
        <button type="button"
            wire:click="selecionarTipoPedido('entrega')"
            class="rounded-2xl border p-5 text-left transition-all
            {{ $tipoPedido === 'entrega' ? 'bg-ink-900 text-white border-ink-900 shadow-lg' : 'bg-white dark:bg-ink-900 border-gray-200 dark:border-ink-700 hover:shadow' }}">
            <div class="text-2xl mb-2">🛵</div>
            <div class="font-bold">Entrega</div>
            <div class="text-xs opacity-70">Buscar cliente por telefone e endereço.</div>
        </button>

        <button type="button"
            wire:click="selecionarTipoPedido('balcao')"
            class="rounded-2xl border p-5 text-left transition-all
            {{ $tipoPedido === 'balcao' ? 'bg-ink-900 text-white border-ink-900 shadow-lg' : 'bg-white dark:bg-ink-900 border-gray-200 dark:border-ink-700 hover:shadow' }}">
            <div class="text-2xl mb-2">📦</div>
            <div class="font-bold">Vem buscar</div>
            <div class="text-xs opacity-70">Pedido para retirada no balcão.</div>
        </button>

        <button type="button"
            wire:click="selecionarTipoPedido('mesa')"
            class="rounded-2xl border p-5 text-left transition-all
            {{ $tipoPedido === 'mesa' ? 'bg-ink-900 text-white border-ink-900 shadow-lg' : 'bg-white dark:bg-ink-900 border-gray-200 dark:border-ink-700 hover:shadow' }}">
            <div class="text-2xl mb-2">🍽️</div>
            <div class="font-bold">Consumo local</div>
            <div class="text-xs opacity-70">Pedido para mesa ou consumo no local.</div>
        </button>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-4">

        {{-- Coluna esquerda --}}
        <section class="xl:col-span-4 space-y-4">

            {{-- Dados cliente / pedido --}}
            <div class="bg-white dark:bg-ink-900 rounded-2xl border border-gray-200 dark:border-ink-700 p-4">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h2 class="font-bold text-ink-900 dark:text-ink-50">Dados do pedido</h2>
                        <p class="text-xs text-ink-500">Tipo: {{ $this->labelTipoPedido() }}</p>
                    </div>

                    @if($tipoPedido === 'entrega' || $tipoPedido === 'balcao')
                        <button type="button"
                            wire:click="$set('mostrarModalTelefone', true)"
                            class="px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 text-xs font-bold hover:bg-blue-100">
                            Buscar telefone
                        </button>
                    @endif
                </div>

                @if(!$tipoPedido)
                    <div class="text-sm text-ink-500 border border-dashed rounded-xl p-4 text-center">
                        Selecione o tipo do pedido para começar.
                    </div>
                @else
                    <div class="space-y-3">
                        @if($tipoPedido === 'mesa')
                            <div>
                                <label class="block text-xs font-bold text-ink-500 mb-1">Mesa / Identificação *</label>
                                <input type="text"
                                    wire:model="mesa"
                                    class="w-full px-3 py-2 rounded-xl border border-gray-300 dark:border-ink-700 dark:bg-ink-800"
                                    placeholder="Ex.: Mesa 4">
                                @error('mesa') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        @endif

                        <div>
                            <label class="block text-xs font-bold text-ink-500 mb-1">Telefone</label>
                            <input type="text"
                                wire:model="telefoneCliente"
                                class="w-full px-3 py-2 rounded-xl border border-gray-300 dark:border-ink-700 dark:bg-ink-800"
                                placeholder="(43) 99999-9999">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-ink-500 mb-1">Cliente</label>
                            <input type="text"
                                wire:model="nomeCliente"
                                class="w-full px-3 py-2 rounded-xl border border-gray-300 dark:border-ink-700 dark:bg-ink-800"
                                placeholder="Nome do cliente">
                            @error('nomeCliente') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        @if($tipoPedido === 'entrega')
                            <div>
                                <label class="block text-xs font-bold text-ink-500 mb-1">Endereço *</label>
                                <input type="text"
                                    wire:model="enderecoCliente"
                                    class="w-full px-3 py-2 rounded-xl border border-gray-300 dark:border-ink-700 dark:bg-ink-800"
                                    placeholder="Rua, avenida...">
                                @error('enderecoCliente') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-bold text-ink-500 mb-1">Número *</label>
                                    <input type="text"
                                        wire:model="numeroCliente"
                                        class="w-full px-3 py-2 rounded-xl border border-gray-300 dark:border-ink-700 dark:bg-ink-800">
                                    @error('numeroCliente') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-ink-500 mb-1">Complemento</label>
                                    <input type="text"
                                        wire:model="complementoCliente"
                                        class="w-full px-3 py-2 rounded-xl border border-gray-300 dark:border-ink-700 dark:bg-ink-800">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-bold text-ink-500 mb-1">Bairro *</label>
                                    <input type="text"
                                        wire:model="bairroCliente"
                                        class="w-full px-3 py-2 rounded-xl border border-gray-300 dark:border-ink-700 dark:bg-ink-800">
                                    @error('bairroCliente') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-ink-500 mb-1">Cidade *</label>
                                    <input type="text"
                                        wire:model="cidadeCliente"
                                        class="w-full px-3 py-2 rounded-xl border border-gray-300 dark:border-ink-700 dark:bg-ink-800">
                                    @error('cidadeCliente') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        @endif

                        <div>
                            <label class="block text-xs font-bold text-ink-500 mb-1">Observação</label>
                            <textarea wire:model="observacao"
                                rows="3"
                                class="w-full px-3 py-2 rounded-xl border border-gray-300 dark:border-ink-700 dark:bg-ink-800"
                                placeholder="Ex.: sem cebola, entregar no portão..."></textarea>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Últimos pedidos --}}
            <div class="bg-white dark:bg-ink-900 rounded-2xl border border-gray-200 dark:border-ink-700 p-4">
                <h2 class="font-bold text-ink-900 dark:text-ink-50 mb-3">
                    Últimos pedidos
                </h2>

                @forelse($ultimosPedidos as $pedido)
                    <div class="border rounded-xl p-3 mb-2 dark:border-ink-700">
                        <div class="flex items-center justify-between gap-2">
                            <div>
                                <div class="font-bold text-sm">#{{ $pedido['numero_pedido'] }}</div>
                                <div class="text-xs text-ink-500">{{ $pedido['created_at'] }}</div>
                            </div>

                            <div class="text-right">
                                <div class="font-bold text-sm">
                                    R$ {{ number_format($pedido['total'], 2, ',', '.') }}
                                </div>

                                <button type="button"
                                    wire:click="repetirPedido({{ $pedido['id'] }})"
                                    class="text-xs text-blue-600 font-bold hover:underline">
                                    Repetir
                                </button>
                            </div>
                        </div>

                        <div class="mt-2 text-xs text-ink-500">
                            @foreach(array_slice($pedido['itens'], 0, 3) as $item)
                                <div>{{ number_format($item['quantidade'], 0, ',', '.') }}x {{ $item['nome'] }}</div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="text-sm text-ink-500 border border-dashed rounded-xl p-4 text-center">
                        Nenhum pedido anterior carregado.
                    </div>
                @endforelse
            </div>
        </section>

        {{-- Coluna direita --}}
        <section class="xl:col-span-8 bg-white dark:bg-ink-900 rounded-2xl border border-gray-200 dark:border-ink-700 overflow-hidden">

            {{-- Entrada rápida --}}
            <div class="p-4 border-b border-gray-200 dark:border-ink-700 bg-gray-50 dark:bg-ink-800/50">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                    <div class="md:col-span-7">
                        <label class="block text-xs font-bold text-ink-500 mb-1">Código do produto</label>
                        <input type="text"
                            id="campo-codigo-atendimento"
                            wire:model="codigoProduto"
                            wire:keydown.enter="adicionarProdutoPorCodigo"
                            class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 dark:border-ink-700 dark:bg-ink-900 text-lg font-bold"
                            placeholder="Digite o código e pressione Enter"
                            autocomplete="off">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-ink-500 mb-1">Quantidade</label>
                        <input type="text"
                            id="campo-quantidade-atendimento"
                            value="1"
                            x-on:input="
                                let valor = $event.target.value.replace(',', '.');
                                $wire.set('quantidadeInput', Number(valor || 1));
                            "
                            class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 dark:border-ink-700 dark:bg-ink-900 text-lg font-bold text-center"
                            autocomplete="off">
                    </div>

                    <div class="md:col-span-3">
                        <button type="button"
                            wire:click="adicionarProdutoPorCodigo"
                            class="w-full px-4 py-3 rounded-xl bg-ink-900 text-white font-bold hover:bg-ink-800">
                            Adicionar
                        </button>
                    </div>
                </div>
            </div>

            {{-- Itens --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-ink-50 dark:bg-ink-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase text-ink-500">Item</th>
                            <th class="px-4 py-3 text-center text-xs font-bold uppercase text-ink-500">Qtd</th>
                            <th class="px-4 py-3 text-right text-xs font-bold uppercase text-ink-500">Unitário</th>
                            <th class="px-4 py-3 text-right text-xs font-bold uppercase text-ink-500">Subtotal</th>
                            <th class="px-4 py-3 text-center text-xs font-bold uppercase text-ink-500">Ações</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-ink-700">
                        @forelse($carrinho as $chave => $item)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-ink-900 dark:text-ink-50">
                                        {{ $item['produto_nome'] }}
                                    </div>
                                    <div class="text-xs text-ink-400">
                                        Código/ID: {{ $item['produto_id'] }}
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <input type="number"
                                        step="0.01"
                                        min="0.01"
                                        value="{{ $item['quantidade'] }}"
                                        wire:change="atualizarQuantidade('{{ $chave }}', $event.target.value)"
                                        class="w-20 px-2 py-1 border rounded-lg text-center dark:bg-ink-800 dark:border-ink-700">
                                </td>

                                <td class="px-4 py-3 text-right">
                                    R$ {{ number_format($item['preco_unitario'], 2, ',', '.') }}
                                </td>

                                <td class="px-4 py-3 text-right font-bold">
                                    R$ {{ number_format($item['subtotal'], 2, ',', '.') }}
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <button type="button"
                                        wire:click="removerItem('{{ $chave }}')"
                                        class="text-red-600 hover:text-red-800 font-bold text-xs">
                                        Remover
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-16 text-center text-ink-400">
                                    Nenhum item adicionado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Rodapé --}}
            <div class="p-4 border-t border-gray-200 dark:border-ink-700 bg-gray-50 dark:bg-ink-800/50">
                <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
                    <div>
                        <div class="text-xs font-bold uppercase text-ink-400">Total de itens</div>
                        <div class="text-sm font-bold">{{ number_format($this->totalItens(), 3, ',', '.') }}</div>
                    </div>

                    <div class="text-left md:text-right">
                        <div class="text-xs font-bold uppercase text-ink-400">Total do pedido</div>
                        <div class="text-3xl font-black text-primary-600">
                            R$ {{ number_format($this->totalCarrinho(), 2, ',', '.') }}
                        </div>
                    </div>

                    <button type="button"
                        wire:click="salvarPedido"
                        class="px-6 py-4 rounded-xl bg-green-600 text-white font-black hover:bg-green-700 disabled:opacity-50"
                        @disabled(empty($carrinho) || !$tipoPedido)>
                        Salvar Pedido
                    </button>
                </div>
            </div>
        </section>
    </div>

    {{-- Modal telefone --}}
    @if($mostrarModalTelefone)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
            <div class="bg-white dark:bg-ink-900 rounded-2xl w-full max-w-md p-6 shadow-xl">
                <h2 class="text-xl font-bold text-ink-900 dark:text-ink-50 mb-2">
                    Buscar cliente
                </h2>

                <p class="text-sm text-ink-500 mb-4">
                    Digite o telefone para carregar cadastro e últimos pedidos.
                </p>

                <div>
                    <label class="block text-xs font-bold text-ink-500 mb-1">Telefone</label>
                    <input type="text"
                        id="telefone-atendimento"
                        wire:model="telefoneBusca"
                        wire:keydown.enter="buscarClientePorTelefone"
                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-300 dark:border-ink-700 dark:bg-ink-800 text-lg font-bold"
                        placeholder="(43) 99999-9999"
                        autocomplete="off">

                    @error('telefoneBusca')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button"
                        wire:click="fecharModalTelefone"
                        class="px-4 py-2 border rounded-lg">
                        Pular
                    </button>

                    <button type="button"
                        wire:click="buscarClientePorTelefone"
                        class="px-4 py-2 bg-ink-900 text-white rounded-lg">
                        Buscar
                    </button>
                </div>
            </div>
        </div>
    @endif

    <script>
        function atendimentoRapido() {
            return {
                init() {
                    this.focarCodigo();

                    window.addEventListener('focar-codigo-atendimento', () => {
                        this.focarCodigo();
                    });

                    window.addEventListener('resetar-quantidade-atendimento', () => {
                        const quantidade = document.getElementById('campo-quantidade-atendimento');

                        if (quantidade) {
                            quantidade.value = '1';
                        }
                    });

                    setTimeout(() => {
                        const telefone = document.getElementById('telefone-atendimento');
                        if (telefone) {
                            telefone.focus();
                            telefone.select();
                        }
                    }, 120);
                },

                focarCodigo() {
                    setTimeout(() => {
                        const campo = document.getElementById('campo-codigo-atendimento');

                        if (campo) {
                            campo.focus();
                            campo.select();
                        }
                    }, 100);
                }
            }
        }
    </script>
</div>