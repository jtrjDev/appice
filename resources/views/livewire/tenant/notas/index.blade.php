<div>
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-ink-900 dark:text-ink-50">Notas Fiscais</h1>
            <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">
                Gerencie as notas fiscais emitidas
            </p>
        </div>
    </div>

    {{-- Cards de resumo --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white dark:bg-ink-900 rounded-lg border p-4">
            <p class="text-xs text-ink-500">Total de Notas</p>
            <p class="text-2xl font-bold">{{ $totais['total'] }}</p>
        </div>
        <div class="bg-white dark:bg-ink-900 rounded-lg border p-4">
            <p class="text-xs text-ink-500">Autorizadas</p>
            <p class="text-2xl font-bold text-green-600">{{ $totais['autorizadas'] }}</p>
        </div>
        <div class="bg-white dark:bg-ink-900 rounded-lg border p-4">
            <p class="text-xs text-ink-500">Processando</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $totais['processando'] }}</p>
        </div>
        <div class="bg-white dark:bg-ink-900 rounded-lg border p-4">
            <p class="text-xs text-ink-500">Rejeitadas</p>
            <p class="text-2xl font-bold text-red-600">{{ $totais['rejeitadas'] }}</p>
        </div>
        <div class="bg-white dark:bg-ink-900 rounded-lg border p-4">
            <p class="text-xs text-ink-500">Erro</p>
            <p class="text-2xl font-bold text-orange-600">{{ $totais['erro'] }}</p>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="bg-white dark:bg-ink-900 rounded-lg border p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
            <div class="md:col-span-2">
                <input type="text" wire:model.live.debounce.300ms="search" 
                    placeholder="Buscar por pedido, número ou chave..."
                    class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div>
                <select wire:model.live="statusFilter" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">Todos status</option>
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select wire:model.live="modeloFilter" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">Todos modelos</option>
                    @foreach($modeloOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <input type="date" wire:model.live="dataInicio" class="w-full px-3 py-2 border rounded-lg" placeholder="Data início">
            </div>
            <div>
                <input type="date" wire:model.live="dataFim" class="w-full px-3 py-2 border rounded-lg" placeholder="Data fim">
            </div>
            <div>
                <button wire:click="limparFiltros" class="w-full px-3 py-2 border rounded-lg text-ink-600 hover:bg-ink-50">
                    Limpar Filtros
                </button>
            </div>
        </div>
    </div>

    {{-- Tabela de Notas --}}
    <div class="bg-white dark:bg-ink-900 rounded-lg border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-ink-50 dark:bg-ink-800 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-ink-500 uppercase">Pedido</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-ink-500 uppercase">Nº Nota</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-ink-500 uppercase">Modelo</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-ink-500 uppercase">Chave de Acesso</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-ink-500 uppercase">Data</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-ink-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-ink-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100 dark:divide-ink-800">
                    @forelse($notas as $nota)
                    <tr class="hover:bg-ink-50 dark:hover:bg-ink-800/50 transition-colors">
                        <td class="px-4 py-3">
                            <span class="font-mono text-sm">#{{ $nota->pedido->numero_pedido ?? 'N/A' }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm font-mono">
                            {{ $nota->numero_nota ?? '-' }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-1 rounded text-xs font-medium bg-ink-100">
                                {{ strtoupper($nota->modelo) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs font-mono">
                            {{ $nota->chave_acesso ? substr($nota->chave_acesso, 0, 20) . '...' : '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            {{ $nota->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-3">
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
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if($nota->link_xml)
                                <a href="{{ route('tenant.notas.download-xml', $nota) }}" target="_blank"
                                    class="text-blue-500 hover:text-blue-700" title="Download XML">
                                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                </a>
                                @endif
                                @if($nota->link_pdf)
                                <a href="{{ route('tenant.notas.download-pdf', $nota) }}" target="_blank"
                                    class="text-red-500 hover:text-red-700" title="Download PDF">
                                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </a>
                                @endif
                                <a href="{{ route('tenant.notas.show', $nota) }}" wire:navigate
                                    class="text-ink-500 hover:text-ink-700" title="Consultar">
                                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </table>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-ink-500">
                            Nenhuma nota fiscal encontrada
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">
            {{ $notas->links() }}
        </div>
    </div>
</div>