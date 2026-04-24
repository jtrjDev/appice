<div>
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-ink-900 dark:text-ink-50">Usuários do Sistema</h1>
            <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">
                Gerencie todos os usuários da plataforma
            </p>
        </div>
        <a href="{{ route('admin.usuarios.create') }}" 
           wire:navigate
           class="inline-flex items-center gap-2 px-4 py-2 bg-ink-900 text-white rounded-lg hover:bg-ink-800 transition-colors">
            <x-ui.icon name="plus" class="size-4" />
            Novo Usuário
        </a>
    </div>

    {{-- Filtros --}}
    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   placeholder="Buscar por nome ou e-mail..."
                   class="w-full px-3 py-2 border border-ink-300 dark:border-ink-700 rounded-lg bg-white dark:bg-ink-800 text-ink-900 dark:text-ink-100 focus:ring-2 focus:ring-ink-500">
        </div>
        <div>
            <select wire:model.live="filterTenant" 
                    class="w-full px-3 py-2 border border-ink-300 dark:border-ink-700 rounded-lg bg-white dark:bg-ink-800 text-ink-900 dark:text-ink-100">
                <option value="">Todos os tenants</option>
                <option value="super">Super Admin (Sistema)</option>
                @foreach($tenants as $tenant)
                    <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                @endforeach
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
                        <th class="text-left px-6 py-3 text-xs font-medium text-ink-500 uppercase">Nome</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-ink-500 uppercase">E-mail</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-ink-500 uppercase">Tenant</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-ink-500 uppercase">Tipo</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-ink-500 uppercase">Status</th>
                        <th class="text-right px-6 py-3 text-xs font-medium text-ink-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-200 dark:divide-ink-800">
                    @forelse($usuarios as $usuario)
                        <tr class="hover:bg-ink-50 dark:hover:bg-ink-800/50 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-ink-900 dark:text-ink-100">
                                {{ $usuario->name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-ink-600 dark:text-ink-300">
                                {{ $usuario->email }}
                            </td>
                            <td class="px-6 py-4">
                                @if($usuario->tenant_id)
                                    @php $tenant = $tenants->firstWhere('id', $usuario->tenant_id) @endphp
                                    <span class="text-sm text-ink-600">{{ $tenant->name ?? $usuario->tenant_id }}</span>
                                @else
                                    <span class="inline-flex px-2 py-1 rounded text-xs font-medium bg-purple-100 text-purple-700">
                                        Sistema
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($usuario->is_super_admin)
                                    <span class="inline-flex px-2 py-1 rounded text-xs font-medium bg-purple-100 text-purple-700">
                                        Super Admin
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-700">
                                        Admin Tenant
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $usuario->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $usuario->is_active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.usuarios.edit', $usuario) }}" 
                                       wire:navigate
                                       class="text-ink-500 hover:text-ink-700">
                                        <x-ui.icon name="edit" class="size-4" />
                                    </a>
                                    @if($usuario->id != auth()->id())
                                    <button wire:click="confirmDelete({{ $usuario->id }})"
                                            class="text-danger-500 hover:text-danger-700">
                                        <x-ui.icon name="trash" class="size-4" />
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-ink-500">
                                Nenhum usuário encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-ink-200 dark:border-ink-800">
            {{ $usuarios->links() }}
        </div>
    </div>

    {{-- Modal de confirmação --}}
    @if($confirmingDelete)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
                <div class="relative bg-white dark:bg-ink-900 rounded-lg max-w-md w-full p-6">
                    <h3 class="text-lg font-medium text-ink-900 dark:text-ink-50 mb-4">Confirmar exclusão</h3>
                    <p class="text-sm text-ink-500 dark:text-ink-400 mb-6">
                        Tem certeza que deseja excluir este usuário? Esta ação não pode ser desfeita.
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