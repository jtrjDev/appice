<div>
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-ink-900 dark:text-ink-50">Histórico de Caixa</h1>
            <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">
                Consulte fechamentos anteriores e reimprima relatórios
            </p>
        </div>
        <a href="{{ route('tenant.caixa') }}" wire:navigate
            class="inline-flex items-center gap-2 px-4 py-2 bg-ink-900 text-white rounded-lg hover:bg-ink-800 transition-colors">
            <x-ui.icon name="arrow-left" class="size-4" />
            Voltar ao Caixa
        </a>
    </div>

    {{-- Filtros --}}
    <div class="bg-white dark:bg-ink-900 rounded-lg border p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <input type="text" wire:model.live.debounce.300ms="search" 
                    placeholder="Buscar por ID ou operador..."
                    class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div>
                <select wire:model.live="statusFilter" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">Todos os status</option>
                    <option value="aberto">Aberto</option>
                    <option value="fechado">Fechado</option>
                </select>
            </div>
            <div>
                <select wire:model.live="perPage" class="w-full px-3 py-2 border rounded-lg">
                    <option value="15">15 por página</option>
                    <option value="25">25 por página</option>
                    <option value="50">50 por página</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Tabela --}}
    <div class="bg-white dark:bg-ink-900 rounded-lg border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-ink-50 dark:bg-ink-800 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-ink-500 uppercase">Caixa</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-ink-500 uppercase">Abertura</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-ink-500 uppercase">Fechamento</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-ink-500 uppercase">Operador</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-ink-500 uppercase">Total Vendas</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-ink-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100">
                    @forelse($caixas as $caixa)
                    <tr class="hover:bg-ink-50 transition-colors">
                        <td class="px-4 py-3 font-mono text-sm">#{{ $caixa->id }}
                            <div class="text-xs text-ink-400">Status: 
                                <span class="{{ $caixa->status == 'aberto' ? 'text-green-600' : 'text-gray-600' }}">
                                    {{ ucfirst($caixa->status) }}
                                </span>
                            </div>
                            <div class="text-xs text-ink-400">Vendas: {{ $caixa->quantidade_vendas }}</div>
                        </tr>
                        <td class="px-4 py-3 text-sm">{{ \Carbon\Carbon::parse($caixa->aberto_em)->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-sm">
                            @if($caixa->fechado_em)
                                {{ \Carbon\Carbon::parse($caixa->fechado_em)->format('d/m/Y H:i') }}
                            @else
                                <span class="text-green-600 text-xs">Em aberto</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm">{{ $caixa->operador->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-right font-semibold">R$ {{ number_format($caixa->total_vendas, 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <button wire:click="abrirRelatorio({{ $caixa->id }})"
                                class="inline-flex items-center gap-1 px-3 py-1 bg-ink-100 text-ink-700 rounded-lg text-xs hover:bg-ink-200 transition-colors">
                                <x-ui.icon name="printer" class="size-3" />
                                Ver / Imprimir
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-ink-500">
                            Nenhum caixa encontrado
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">
            {{ $caixas->links() }}
        </div>
    </div>

    {{-- MODAL DO RELATÓRIO --}}
    @if($mostrarRelatorio && $relatorioCaixa)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60" wire:click="fecharRelatorio"></div>
        
        <div class="relative bg-white dark:bg-ink-900 rounded-xl shadow-xl w-full max-w-3xl max-h-[90vh] overflow-hidden border border-gray-200 dark:border-ink-700">
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-ink-700">
                <div>
                    <h2 class="text-lg font-semibold text-ink-900 dark:text-ink-50">
                        Relatório de Fechamento - Caixa #{{ $relatorioCaixa->id }}
                    </h2>
                    <p class="text-sm text-ink-500 dark:text-ink-400">
                        {{ \Carbon\Carbon::parse($relatorioCaixa->fechado_em ?? $relatorioCaixa->aberto_em)->format('d/m/Y H:i') }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="window.print();"
                        class="px-3 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-sm font-medium hover:bg-ink-50 dark:hover:bg-ink-800">
                        <x-ui.icon name="printer" class="size-4 inline" />
                        Imprimir
                    </button>
                    <button wire:click="fecharRelatorio"
                        class="px-3 py-2 border border-gray-300 dark:border-ink-600 rounded-lg text-sm font-medium hover:bg-ink-50 dark:hover:bg-ink-800">
                        Fechar
                    </button>
                </div>
            </div>

            <div class="p-6 overflow-y-auto max-h-[calc(90vh-76px)] bg-ink-50 dark:bg-ink-950">
                <div id="relatorio-print-area" class="mx-auto bg-white text-black shadow rounded-lg p-6" style="width: 320px; font-family: monospace; font-size: 11px;">
                    @php
                        $configImpressao = \App\Models\Tenant\Configuracao::first();
                        $saldoEsperado = $relatorioCaixa->saldo_inicial + $relatorioCaixa->total_vendas;
                        $diferenca = ($relatorioCaixa->saldo_final ?? 0) - $saldoEsperado;
                    @endphp
                    
                    <div class="text-center">
                        <div class="bold">{{ $configImpressao->razao_social ?? 'ESTABELECIMENTO' }}</div>
                        <div>CNPJ: {{ $configImpressao->cpf_cnpj_formatado ?? '' }}</div>
                        <div>{{ $configImpressao->endereco ?? '' }}, {{ $configImpressao->numero ?? '' }}</div>
                        <div class="bold">RELATÓRIO DE FECHAMENTO</div>
                    </div>

                    <div class="line-dashed" style="border-top:1px dashed #000; margin:6px 0;"></div>

                    <div class="row" style="display:flex; justify-content:space-between;">
                        <span>CAIXA:</span>
                        <span class="bold">#{{ $relatorioCaixa->id }}</span>
                    </div>
                    <div class="row">
                        <span>DATA ABERTURA:</span>
                        <span>{{ \Carbon\Carbon::parse($relatorioCaixa->aberto_em)->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="row">
                        <span>DATA FECHAMENTO:</span>
                        <span>{{ $relatorioCaixa->fechado_em ? \Carbon\Carbon::parse($relatorioCaixa->fechado_em)->format('d/m/Y H:i') : '-' }}</span>
                    </div>
                    <div class="row">
                        <span>OPERADOR:</span>
                        <span>{{ $relatorioCaixa->operador->name ?? '' }}</span>
                    </div>

                    <div class="line-dashed" style="border-top:1px dashed #000; margin:6px 0;"></div>

                    <div class="row bold">
                        <span>RESUMO DE VENDAS</span>
                        <span></span>
                    </div>
                    <div class="row">
                        <span>Quantidade de vendas:</span>
                        <span>{{ $relatorioCaixa->quantidade_vendas }}</span>
                    </div>
                    <div class="row">
                        <span>Total vendido:</span>
                        <span>R$ {{ number_format($relatorioCaixa->total_vendas, 2, ',', '.') }}</span>
                    </div>

                    <div class="line-dashed" style="border-top:1px dashed #000; margin:6px 0;"></div>

                    <div class="row bold">
                        <span>FORMAS DE PAGAMENTO</span>
                        <span></span>
                    </div>
                    <div class="row">
                        <span>Dinheiro:</span>
                        <span>R$ {{ number_format($relatorioCaixa->total_dinheiro, 2, ',', '.') }}</span>
                    </div>
                    <div class="row">
                        <span>Cartão Crédito:</span>
                        <span>R$ {{ number_format($relatorioCaixa->total_credito, 2, ',', '.') }}</span>
                    </div>
                    <div class="row">
                        <span>Cartão Débito:</span>
                        <span>R$ {{ number_format($relatorioCaixa->total_debito, 2, ',', '.') }}</span>
                    </div>
                    <div class="row">
                        <span>PIX:</span>
                        <span>R$ {{ number_format($relatorioCaixa->total_pix, 2, ',', '.') }}</span>
                    </div>

                    <div class="line-solid" style="border-top:2px solid #000; margin:6px 0;"></div>

                    <div class="row bold">
                        <span>TOTAL GERAL</span>
                        <span>R$ {{ number_format($relatorioCaixa->total_vendas, 2, ',', '.') }}</span>
                    </div>

                    <div class="line-dashed" style="border-top:1px dashed #000; margin:6px 0;"></div>

                    <div class="row bold">
                        <span>FECHAMENTO</span>
                        <span></span>
                    </div>
                    <div class="row">
                        <span>Saldo inicial:</span>
                        <span>R$ {{ number_format($relatorioCaixa->saldo_inicial, 2, ',', '.') }}</span>
                    </div>
                    <div class="row">
                        <span>Saldo esperado:</span>
                        <span>R$ {{ number_format($saldoEsperado, 2, ',', '.') }}</span>
                    </div>
                    <div class="row">
                        <span>Saldo declarado:</span>
                        <span>R$ {{ number_format($relatorioCaixa->saldo_final ?? 0, 2, ',', '.') }}</span>
                    </div>
                    <div class="row">
                        <span>Diferença:</span>
                        <span class="{{ $diferenca > 0 ? 'text-green' : ($diferenca < 0 ? 'text-red' : '') }}">
                            R$ {{ number_format(abs($diferenca), 2, ',', '.') }}
                            {{ $diferenca > 0 ? '(sobra)' : ($diferenca < 0 ? '(falta)' : '') }}
                        </span>
                    </div>

                    @if($relatorioCaixa->observacao)
                    <div class="line-dashed" style="border-top:1px dashed #000; margin:6px 0;"></div>
                    <div class="row">
                        <span>OBS:</span>
                        <span>{{ $relatorioCaixa->observacao }}</span>
                    </div>
                    @endif

                    <div class="line-dashed" style="border-top:1px dashed #000; margin:6px 0;"></div>

                    <div class="footer" style="text-align:center; font-size:9px; margin-top:10px;">
                        <div>________________________________</div>
                        <div>Assinatura do Operador</div>
                        <div>{{ $relatorioCaixa->fechado_em ? \Carbon\Carbon::parse($relatorioCaixa->fechado_em)->format('d/m/Y H:i:s') : '' }}</div>
                        <div>** Este documento é um resumo das operações do caixa **</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
    function imprimirRelatorio() {
        const conteudo = document.getElementById('relatorio-print-area')?.innerHTML;
        if (!conteudo) return;
        const janela = window.open('', '_blank', 'width=400,height=700');
        janela.document.write(`
            <html>
                <head>
                    <title>Relatório de Fechamento</title>
                    <style>
                        * { margin: 0; padding: 0; box-sizing: border-box; }
                        body {
                            font-family: monospace;
                            width: 280px;
                            margin: 0 auto;
                            padding: 12px;
                            font-size: 11px;
                            line-height: 1.3;
                            background: white;
                        }
                        .text-center { text-align: center; }
                        .row { display: flex; justify-content: space-between; margin: 3px 0; }
                        .bold { font-weight: bold; }
                        .line-dashed { border-top: 1px dashed #000; margin: 6px 0; }
                        .line-solid { border-top: 2px solid #000; margin: 6px 0; }
                        .footer { text-align: center; font-size: 9px; margin-top: 10px; }
                        .text-green { color: #2ecc71; }
                        .text-red { color: #e74c3c; }
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