<div>
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-ink-900 dark:text-ink-50">Produtos</h1>
            <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">
                Gerencie seus produtos
            </p>
        </div>
        <a href="{{ route('tenant.produtos.create') }}" 
           wire:navigate
           class="inline-flex items-center gap-2 px-4 py-2 bg-ink-900 text-white rounded-lg hover:bg-ink-800 transition-colors">
            <x-ui.icon name="plus" class="size-4" />
            Novo Produto
        </a>
    </div>

    {{-- Filtros --}}
    <div class="mb-6 grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="md:col-span-2">
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   placeholder="Buscar por nome, código ou NCM..."
                   class="w-full px-3 py-2 border border-ink-300 dark:border-ink-700 rounded-lg bg-white dark:bg-ink-800 text-ink-900 dark:text-ink-100 focus:ring-2 focus:ring-ink-500">
        </div>
        <div>
            <select wire:model.live="categoriaFilter" 
                    class="w-full px-3 py-2 border border-ink-300 dark:border-ink-700 rounded-lg bg-white dark:bg-ink-800 text-ink-900 dark:text-ink-100">
                <option value="">Todas categorias</option>
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <select wire:model.live="statusFilter" 
                    class="w-full px-3 py-2 border border-ink-300 dark:border-ink-700 rounded-lg bg-white dark:bg-ink-800 text-ink-900 dark:text-ink-100">
                <option value="">Todos status</option>
                <option value="ativo">Ativos</option>
                <option value="inativo">Inativos</option>
                <option value="destaque">Destaques</option>
                <option value="estoque_baixo">Estoque Baixo</option>
            </select>
        </div>
        <div>
            <select wire:model.live="perPage" 
                    class="w-full px-3 py-2 border border-ink-300 dark:border-ink-700 rounded-lg bg-white dark:bg-ink-800 text-ink-900 dark:text-ink-100">
                <option value="15">15 por página</option>
                <option value="25">25 por página</option>
                <option value="50">50 por página</option>
                <option value="100">100 por página</option>
            </select>
        </div>
    </div>

    {{-- Tabela --}}
    <div class="bg-white dark:bg-ink-900 rounded-lg border border-ink-200 dark:border-ink-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-ink-50 dark:bg-ink-800 border-b border-ink-200 dark:border-ink-700">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-medium text-ink-500 uppercase">Imagem</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-ink-500 uppercase">Código</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-ink-500 uppercase">Produto</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-ink-500 uppercase">Categoria</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-ink-500 uppercase">Preço</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-ink-500 uppercase">Estoque</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-ink-500 uppercase">Status</th>
                        <th class="text-right px-6 py-3 text-xs font-medium text-ink-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-200 dark:divide-ink-800">
                    @forelse($produtos as $produto)
                        <tr class="hover:bg-ink-50 dark:hover:bg-ink-800/50 transition-colors">
                            <td class="px-6 py-4">
                                @if($produto->imagem)
                                    <img src="{{ Storage::url($produto->imagem) }}" class="w-10 h-10 object-cover rounded">
                                @else
                                    <div class="w-10 h-10 bg-ink-100 dark:bg-ink-800 rounded flex items-center justify-center">
                                        <x-ui.icon name="package" class="size-5 text-ink-400" />
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-mono text-ink-600 dark:text-ink-300">
                                {{ $produto->codigo ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-ink-900 dark:text-ink-100">{{ $produto->nome }}</p>
                                @if($produto->destaque)
                                    <span class="text-xs text-yellow-600">⭐ Destaque</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-ink-600 dark:text-ink-300">
                                {{ $produto->categoria->nome ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-ink-900 dark:text-ink-100">
                                    {{ $produto->preco_atual_formatado }}
                                </p>
                                @if($produto->preco_promocional)
                                    <p class="text-xs text-ink-500 line-through">{{ $produto->preco_formatado }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm {{ $produto->estoque <= $produto->estoque_minimo ? 'text-red-600 font-semibold' : 'text-ink-600' }}">
                                    {{ $produto->estoque }} {{ $produto->unidade_medida }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $produto->ativo ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                        {{ $produto->ativo ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('tenant.produtos.edit', $produto) }}" 
                                       wire:navigate
                                       class="text-ink-500 hover:text-ink-700">
                                        <x-ui.icon name="edit" class="size-4" />
                                    </a>
                                    <button wire:click="confirmDelete({{ $produto->id }})"
                                            class="text-danger-500 hover:text-danger-700">
                                        <x-ui.icon name="trash" class="size-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-ink-500">
                                Nenhum produto encontrado.
                                <a href="{{ route('tenant.produtos.create') }}" wire:navigate class="text-ink-900 underline ml-1">
                                    Criar primeiro produto
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-ink-200 dark:border-ink-800">
            {{ $produtos->links() }}
        </div>
    </div>

    {{-- Modal de confirmação de exclusão --}}
    @if($confirmingDelete)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
                <div class="relative bg-white dark:bg-ink-900 rounded-lg max-w-md w-full p-6">
                    <h3 class="text-lg font-medium text-ink-900 dark:text-ink-50 mb-4">Confirmar exclusão</h3>
                    <p class="text-sm text-ink-500 dark:text-ink-400 mb-6">
                        Tem certeza que deseja excluir este produto? Esta ação não pode ser desfeita.
                    </p>
                    <div class="flex justify-end gap-3">
                        <button wire:click="$set('confirmingDelete', false)"
                                class="px-4 py-2 border border-ink-300 rounded-lg text-ink-700 hover:bg-ink-50">
                            Cancelar
                        </button>
                        <button wire:click="delete"
                                class="px-4 py-2 bg-danger-600 text-white rounded-lg hover:bg-danger-700">
                            Excluir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>