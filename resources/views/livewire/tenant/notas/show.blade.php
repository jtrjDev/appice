<div>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-ink-900 dark:text-ink-50">Detalhes da Nota Fiscal</h1>
            <p class="text-sm text-ink-500 dark:text-ink-400">
                Nota #{{ $nota->referencia }}
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('tenant.notas.index') }}" wire:navigate
                class="px-4 py-2 border rounded-lg text-ink-700 hover:bg-ink-50">
                Voltar
            </a>
            <button wire:click="reimprimirCupom"
                class="px-4 py-2 bg-ink-900 text-white rounded-lg hover:bg-ink-800">
                Reimprimir Cupom
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="space-y-6">
            {{-- Informações da Nota --}}
            <div class="bg-white dark:bg-ink-900 rounded-lg border p-6">
                <h2 class="text-lg font-semibold mb-4">Informações da Nota Fiscal</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-ink-500">Referência</p>
                        <p class="font-medium">{{ $nota->referencia }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-ink-500">Número da Nota</p>
                        <p class="font-medium">{{ $nota->numero_nota ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-ink-500">Modelo</p>
                        <p class="font-medium">{{ strtoupper($nota->modelo) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-ink-500">Status</p>
                        @php
                            $statusColors = [
                                'processando' => 'bg-yellow-100 text-yellow-700',
                                'autorizada' => 'bg-green-100 text-green-700',
                                'rejeitada' => 'bg-red-100 text-red-700',
                                'erro' => 'bg-orange-100 text-orange-700',
                            ];
                        @endphp
                        <span class="inline-flex px-2 py-1 rounded text-xs font-medium {{ $statusColors[$nota->status] ?? 'bg-gray-100' }}">
                            {{ ucfirst($nota->status) }}
                        </span>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs text-ink-500">Chave de Acesso</p>
                        <p class="text-sm font-mono break-all">{{ $nota->chave_acesso ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-ink-500">Data de Emissão</p>
                        <p>{{ $nota->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                </div>
            </div>

            {{-- Mensagem de Erro --}}
            @if($nota->mensagem_erro)
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-red-700 dark:text-red-400 mb-2">Erro na Emissão</h3>
                <p class="text-sm text-red-600 dark:text-red-300">{{ $nota->mensagem_erro }}</p>
            </div>
            @endif
        </div>

        <div class="space-y-6">
            {{-- Informações do Pedido --}}
            <div class="bg-white dark:bg-ink-900 rounded-lg border p-6">
                <h2 class="text-lg font-semibold mb-4">Pedido Relacionado</h2>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-ink-500">Número do Pedido</span>
                        <span class="font-medium">#{{ $nota->pedido->numero_pedido ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-ink-500">Valor Total</span>
                        <span class="font-bold text-green-600">R$ {{ number_format($nota->pedido->total ?? 0, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Links para Download --}}
            <div class="bg-white dark:bg-ink-900 rounded-lg border p-6">
                <h2 class="text-lg font-semibold mb-4">Downloads</h2>
                <div class="flex gap-3">
                    @if($nota->link_xml)
                    <a href="{{ $nota->link_xml }}" target="_blank" class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        XML
                    </a>
                    @endif
                    @if($nota->link_pdf)
                    <a href="{{ $nota->link_pdf }}" target="_blank" class="flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        PDF
                    </a>
                    @endif
                    @if(!$nota->link_xml && !$nota->link_pdf)
                    <p class="text-sm text-ink-500">Documentos ainda não disponíveis</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>