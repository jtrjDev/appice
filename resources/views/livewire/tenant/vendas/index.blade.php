<div x-data="{}"
    x-on:toast.window="$data.addToast($event.detail.type, $event.detail.message)">

    {{-- MODAL PARA CPF/CNPJ --}}
    @if($mostrarModalNF)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white dark:bg-ink-900 rounded-lg w-full max-w-md p-6">
            <h2 class="text-xl font-semibold mb-4">Emitir Nota Fiscal</h2>
            <p class="text-sm text-ink-500 mb-4">Pedido #{{ $pedidoSelecionado?->numero_pedido }}</p>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">CPF / CNPJ</label>
                    <input type="text" wire:model="cpfCnpjNF" 
                        class="w-full px-3 py-2 border rounded-lg" 
                        placeholder="000.000.000-00 ou 00.000.000/0000-00">
                    @error('cpfCnpjNF') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Nome do Cliente</label>
                    <input type="text" wire:model="nomeClienteNF" 
                        class="w-full px-3 py-2 border rounded-lg" 
                        placeholder="Nome completo">
                    @error('nomeClienteNF') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Tipo de Documento</label>
                    <select wire:model="tipoDocumentoNF" class="w-full px-3 py-2 border rounded-lg">
                        <option value="CPF">CPF (Pessoa Física)</option>
                        <option value="CNPJ">CNPJ (Pessoa Jurídica)</option>
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 mt-6">
                <button wire:click="fecharModalNF" class="px-4 py-2 border rounded-lg">Cancelar</button>
                <button wire:click="emitirNotaComDocumento" class="px-4 py-2 bg-ink-900 text-white rounded-lg">Emitir Nota</button>
            </div>
        </div>
    </div>
    @endif

    {{-- CABEÇALHO --}}
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold text-ink-900 dark:text-ink-50">Histórico de Vendas</h1>
            <p class="text-sm text-ink-500 dark:text-ink-400">Consulte vendas, reimprima cupons e acompanhe notas fiscais</p>
        </div>

        <a href="{{ route('tenant.pdv') }}"
           class="px-4 py-2 bg-ink-900 dark:bg-ink-100 text-white dark:text-ink-900 rounded-lg text-sm font-medium hover:bg-ink-800 transition-colors">
            + Nova Venda
        </a>
    </div>

    {{-- FILTROS --}}
    <div class="bg-white dark:bg-ink-900 border border-gray-200 dark:border-ink-700 rounded-xl p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-3">
            <div class="xl:col-span-2">
                <label class="block text-xs text-ink-500 mb-1">Buscar</label>
                <input type="text" wire:model.live.debounce.400ms="busca"
                       placeholder="Pedido, mesa ou cliente"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-sm dark:bg-ink-800 dark:text-ink-100">
            </div>

            <div>
                <label class="block text-xs text-ink-500 mb-1">De</label>
                <input type="date" wire:model.live="dataInicio"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-sm dark:bg-ink-800 dark:text-ink-100">
            </div>

            <div>
                <label class="block text-xs text-ink-500 mb-1">Até</label>
                <input type="date" wire:model.live="dataFim"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-sm dark:bg-ink-800 dark:text-ink-100">
            </div>

            <div>
                <label class="block text-xs text-ink-500 mb-1">Forma</label>
                <select wire:model.live="formaPagamento"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-sm dark:bg-ink-800 dark:text-ink-100">
                    <option value="">Todas</option>
                    <option value="dinheiro">Dinheiro</option>
                    <option value="pix">PIX</option>
                    <option value="cartao_credito">Cartão crédito</option>
                    <option value="cartao_debito">Cartão débito</option>
                </select>
            </div>

            <div>
                <label class="block text-xs text-ink-500 mb-1">Tipo</label>
                <select wire:model.live="tipo"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-sm dark:bg-ink-800 dark:text-ink-100">
                    <option value="">Todos</option>
                    <option value="balcao">Balcão</option>
                    <option value="mesa">Mesa</option>
                </select>
            </div>

            <div>
                <label class="block text-xs text-ink-500 mb-1">Mesa</label>
                <input type="text" wire:model.live.debounce.300ms="mesa"
                       placeholder="Nº da mesa"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-sm dark:bg-ink-800 dark:text-ink-100">
            </div>

            <div>
                <label class="block text-xs text-ink-500 mb-1">Caixa</label>
                <select wire:model.live="caixaId"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-sm dark:bg-ink-800 dark:text-ink-100">
                    <option value="">Todos</option>
                    @foreach($this->caixas as $caixa)
                        <option value="{{ $caixa->id }}">
                            Caixa #{{ $caixa->id }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs text-ink-500 mb-1">Status</label>
                <select wire:model.live="status"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-sm dark:bg-ink-800 dark:text-ink-100">
                    <option value="">Todos</option>
                    <option value="entregue">Entregue</option>
                    <option value="cancelado">Cancelado</option>
                </select>
            </div>

            <div>
                <label class="block text-xs text-ink-500 mb-1">Nota Fiscal</label>
                <select wire:model.live="statusNF"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-sm dark:bg-ink-800 dark:text-ink-100">
                    <option value="">Todas</option>
                    <option value="emitida">Com nota emitida</option>
                    <option value="pendente">Sem nota</option>
                </select>
            </div>
        </div>

        <div class="mt-4 flex justify-end">
            <button wire:click="limparFiltros"
                    class="px-4 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-sm font-medium hover:bg-ink-50 dark:hover:bg-ink-800 transition-colors">
                Limpar filtros
            </button>
        </div>
    </div>

    {{-- TABELA --}}
    <div class="bg-white dark:bg-ink-900 border border-gray-200 dark:border-ink-700 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-ink-50 dark:bg-ink-800">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-ink-500 uppercase">Pedido</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-ink-500 uppercase">Data</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-ink-500 uppercase">Origem</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-ink-500 uppercase">Cliente</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-ink-500 uppercase">Pagamentos</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-ink-500 uppercase">Total</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-ink-500 uppercase">Nota Fiscal</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-ink-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-ink-700">
                    @forelse($this->pedidos as $pedido)
                        @php
                            $notaFiscal = \App\Models\Tenant\NotaFiscal::where('pedido_id', $pedido->id)->first();
                            $temNota = $notaFiscal && $notaFiscal->status === 'autorizada';
                            $notaProcessando = $notaFiscal && $notaFiscal->status === 'processando';
                            $notaErro = $notaFiscal && $notaFiscal->status === 'erro';
                        @endphp
                        <tr class="hover:bg-ink-50 dark:hover:bg-ink-800 transition-colors">
                            <td class="px-4 py-3">
                                <div class="font-semibold text-ink-900 dark:text-ink-50">
                                    #{{ $pedido->numero_pedido }}
                                </div>
                                <div class="text-xs text-ink-400">
                                    Caixa #{{ $pedido->caixa_id }}
                                </div>
                             </td>
                            <td class="px-4 py-3 text-ink-600 dark:text-ink-300">
                                <div>{{ $pedido->created_at->format('d/m/Y') }}</div>
                                <div class="text-xs text-ink-400">{{ $pedido->created_at->format('H:i') }}</div>
                             </td>
                            <td class="px-4 py-3 text-ink-600 dark:text-ink-300">
                                <div class="capitalize">{{ $pedido->tipo }}</div>
                                <div class="text-xs text-ink-400">
                                    {{ $pedido->mesa ? 'Mesa ' . $pedido->mesa : 'Sem mesa' }}
                                </div>
                             </td>
                            <td class="px-4 py-3 text-ink-600 dark:text-ink-300">
                                {{ $pedido->cliente->nome ?? 'Consumidor' }}
                             </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @foreach(($pedido->pagamentos ?? []) as $pag)
                                        <span class="inline-block px-2 py-1 rounded text-xs bg-ink-100 dark:bg-ink-700 text-ink-700 dark:text-ink-300">
                                            {{ str_replace('_', ' ', $pag['forma']) }}
                                            — R$ {{ number_format($pag['valor'], 2, ',', '.') }}
                                        </span>
                                    @endforeach
                                </div>
                             </td>
                            <td class="px-4 py-3 text-right font-semibold text-ink-900 dark:text-ink-50">
                                R$ {{ number_format($pedido->total, 2, ',', '.') }}
                             </td>
                            <td class="px-4 py-3 text-center">
                                @if($temNota)
                                    <div class="flex items-center justify-center gap-1">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-700">
                                            <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Emitida
                                        </span>
                                        @if($notaFiscal->numero_nota)
                                            <button onclick="copiarChave('{{ $notaFiscal->chave_acesso }}')" 
                                                class="text-xs text-blue-500 hover:text-blue-700" title="Copiar chave">
                                                📋
                                            </button>
                                        @endif
                                    </div>
                                @elseif($notaProcessando)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-700">
                                        <svg class="size-3 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Processando
                                    </span>
                                @elseif($notaErro)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-700">
                                        <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Erro
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-500">
                                        Pendente
                                    </span>
                                @endif
                             </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    @if(!$temNota && !$notaProcessando)
                                    <button wire:click="abrirModalNF({{ $pedido->id }})"
                                        class="text-green-500 hover:text-green-700" title="Emitir Nota Fiscal">
                                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </button>
                                    @endif
                                    @if($temNota && $notaFiscal->link_pdf)
                                    <a href="{{ $notaFiscal->link_pdf }}" target="_blank"
                                        class="text-red-500 hover:text-red-700" title="Baixar DANFE">
                                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </a>
                                    @endif
                                    <button wire:click="verVenda({{ $pedido->id }})"
                                        class="px-3 py-1.5 border border-gray-300 dark:border-ink-600 rounded-lg text-xs font-medium hover:bg-ink-50 dark:hover:bg-ink-700">
                                        Ver
                                    </button>
                                    <button wire:click="abrirCupom({{ $pedido->id }})"
                                        class="px-3 py-1.5 bg-ink-900 dark:bg-ink-100 text-white dark:text-ink-900 rounded-lg text-xs font-medium hover:bg-ink-800">
                                        Cupom
                                    </button>
                                </div>
                             </td>
                         </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-ink-400">
                                Nenhuma venda encontrada
                             </td>
                        </tr>
                    @endforelse
                </tbody>
             </table>
        </div>
        <div class="p-4 border-t border-gray-200 dark:border-ink-700">
            {{ $this->pedidos->links() }}
        </div>
    </div>

    {{-- Script para copiar chave --}}
    <script>
        function copiarChave(chave) {
            navigator.clipboard.writeText(chave);
            window.dispatchEvent(new CustomEvent('toast', { 
                detail: { type: 'success', message: 'Chave da nota copiada!' } 
            }));
        }
    </script>

    {{-- MODAL CUPOM --}}
    @if($mostrarCupom && $pedidoCupom)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60" wire:click="fecharCupom"></div>
        <div class="relative bg-white dark:bg-ink-900 rounded-xl shadow-xl w-full max-w-3xl max-h-[90vh] overflow-hidden border border-gray-200 dark:border-ink-700">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-ink-700">
                <div>
                    <h2 class="text-lg font-semibold text-ink-900 dark:text-ink-50">
                        Cupom #{{ $pedidoCupom->numero_pedido }}
                    </h2>
                    <p class="text-sm text-ink-500 dark:text-ink-400">
                        {{ $pedidoCupom->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="imprimirCupom()"
                        class="px-3 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-sm font-medium hover:bg-ink-50 dark:hover:bg-ink-800">
                        Imprimir
                    </button>
                    <a href="{{ route('tenant.vendas.cupom.pdf', ['id' => $pedidoCupom->id]) }}" class="px-3 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-sm font-medium hover:bg-ink-50 dark:hover:bg-ink-800">
                        Baixar PDF
                    </a>
                    <button wire:click="fecharCupom"
                        class="px-3 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-sm font-medium hover:bg-ink-50 dark:hover:bg-ink-800">
                        Fechar
                    </button>
                </div>
            </div>
            <div class="p-6 overflow-y-auto max-h-[calc(90vh-76px)] bg-ink-50 dark:bg-ink-950">
                <div id="cupom-print-area" class="mx-auto bg-white text-black shadow rounded-lg p-6" style="width: 320px; font-family: monospace; font-size: 12px;">
                    <div class="text-center">
                        <div style="font-weight: bold;">{{ tenant()->name }}</div>
                        <div>Cupom não fiscal</div>
                    </div>
                    <hr style="border-top:1px dashed #000; margin:8px 0;">
                    <div>Pedido: #{{ $pedidoCupom->numero_pedido }}</div>
                    <div>Data: {{ $pedidoCupom->created_at->format('d/m/Y H:i') }}</div>
                    <div>Tipo: {{ ucfirst($pedidoCupom->tipo) }}</div>
                    <div>Mesa: {{ $pedidoCupom->mesa ?: '-' }}</div>
                    <div>Cliente: {{ $pedidoCupom->cliente->nome ?? 'Consumidor' }}</div>
                    <hr style="border-top:1px dashed #000; margin:8px 0;">
                    @foreach($pedidoCupom->itens as $item)
                        <div>{{ $item->produto_nome }}</div>
                        <div style="display:flex; justify-content:space-between;">
                            <span>
                                {{ number_format($item->quantidade, $item->quantidade == intval($item->quantidade) ? 0 : 3, ',', '.') }}
                                x {{ number_format($item->preco_unitario, 2, ',', '.') }}
                            </span>
                            <span>{{ number_format($item->subtotal, 2, ',', '.') }}</span>
                        </div>
                    @endforeach
                    <hr style="border-top:1px dashed #000; margin:8px 0;">
                    @foreach(($pedidoCupom->pagamentos ?? []) as $pag)
                        <div style="display:flex; justify-content:space-between;">
                            <span>{{ str_replace('_', ' ', $pag['forma']) }}</span>
                            <span>{{ number_format($pag['valor'], 2, ',', '.') }}</span>
                        </div>
                    @endforeach
                    <hr style="border-top:1px dashed #000; margin:8px 0;">
                    <div style="display:flex; justify-content:space-between; font-weight:bold;">
                        <span>TOTAL</span>
                        <span>R$ {{ number_format($pedidoCupom->total, 2, ',', '.') }}</span>
                    </div>
                    <hr style="border-top:1px dashed #000; margin:8px 0;">
                    <div class="text-center">
                        Obrigado pela preferência
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    function imprimirCupom() {
        const conteudo = document.getElementById('cupom-print-area')?.innerHTML;
        if (!conteudo) return;
        const janela = window.open('', '_blank', 'width=400,height=700');
        janela.document.write(`
            <html>
                <head>
                    <title>Cupom</title>
                    <style>
                        body {
                            font-family: monospace;
                            width: 320px;
                            margin: 0 auto;
                            padding: 12px;
                            font-size: 12px;
                            color: #000;
                        }
                    </style>
                </head>
                <body>${conteudo}</body>
            </html>
        `);
        janela.document.close();
        janela.focus();
        janela.print();
    }
</script>
@endpush