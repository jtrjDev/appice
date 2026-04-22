<div>
    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-ink-900 dark:text-ink-50">Tenants</h1>
                <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">
                    Gerencie todos os tenants da plataforma
                </p>
            </div>
            <a href="{{ route('admin.tenants.create') }}" 
               wire:navigate
               class="inline-flex items-center gap-2 px-4 py-2 bg-ink-900 text-white rounded-lg hover:bg-ink-800 transition-colors">
                <x-ui.icon name="plus" class="size-4" />
                Novo Tenant
            </a>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   placeholder="Buscar por ID, nome ou e-mail..."
                   class="w-full px-3 py-2 border border-ink-300 dark:border-ink-700 rounded-lg bg-white dark:bg-ink-800 text-ink-900 dark:text-ink-100 focus:ring-2 focus:ring-ink-500">
        </div>
        
        <div>
            <select wire:model.live="statusFilter" 
                    class="w-full px-3 py-2 border border-ink-300 dark:border-ink-700 rounded-lg bg-white dark:bg-ink-800 text-ink-900 dark:text-ink-100">
                <option value="">Todos os status</option>
                @foreach($statusOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        
        <div>
            <select wire:model.live="planFilter" 
                    class="w-full px-3 py-2 border border-ink-300 dark:border-ink-700 rounded-lg bg-white dark:bg-ink-800 text-ink-900 dark:text-ink-100">
                <option value="">Todos os planos</option>
                @foreach($plans as $plan)
                    <option value="{{ $plan->id }}">{{ $plan->name }}</option>
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

    {{-- Tabela de Tenants --}}
    <div class="bg-white dark:bg-ink-900 rounded-lg border border-ink-200 dark:border-ink-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-ink-50 dark:bg-ink-800 border-b border-ink-200 dark:border-ink-700">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-medium text-ink-500 dark:text-ink-400 uppercase">ID</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-ink-500 dark:text-ink-400 uppercase">Nome</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-ink-500 dark:text-ink-400 uppercase">E-mail</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-ink-500 dark:text-ink-400 uppercase">Plano</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-ink-500 dark:text-ink-400 uppercase">Status</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-ink-500 dark:text-ink-400 uppercase">Criado em</th>
                        <th class="text-right px-6 py-3 text-xs font-medium text-ink-500 dark:text-ink-400 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-200 dark:divide-ink-800">
                    @forelse($tenants as $tenant)
                        <tr class="hover:bg-ink-50 dark:hover:bg-ink-800/50 transition-colors">
                            <td class="px-6 py-4 text-sm font-mono text-ink-600 dark:text-ink-300">
                                {{ $tenant->id }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-ink-900 dark:text-ink-100">
                                {{ $tenant->name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-ink-600 dark:text-ink-300">
                                {{ $tenant->email }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-ink-100 text-ink-700 dark:bg-ink-800 dark:text-ink-300">
                                    {{ $tenant->plan->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'active' => 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400',
                                        'inactive' => 'bg-gray-100 text-gray-700 dark:bg-gray-500/10 dark:text-gray-400',
                                        'trial' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $statusColors[$tenant->status] }}">
                                    {{ ucfirst($tenant->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-ink-500 dark:text-ink-400">
                                {{ $tenant->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.tenants.edit', $tenant) }}" 
                                       wire:navigate
                                       class="text-ink-500 hover:text-ink-700 dark:text-ink-400 dark:hover:text-ink-200 transition-colors">
                                        <x-ui.icon name="edit" class="size-4" />
                                    </a>
                                    <button type="button" 
                                            wire:click="confirmDelete('{{ $tenant->id }}')"
                                            class="text-danger-500 hover:text-danger-700 transition-colors">
                                        <x-ui.icon name="trash" class="size-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-ink-500 dark:text-ink-400">
                                Nenhum tenant encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-ink-200 dark:border-ink-800">
            {{ $tenants->links() }}
        </div>
    </div>

    {{-- Modal de Confirmação de Delete --}}
    @if($confirmingTenantDeletion)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                
                <div class="inline-block align-bottom bg-white dark:bg-ink-900 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-ink-900 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-500/10 sm:mx-0 sm:h-10 sm:w-10">
                                <x-ui.icon name="alert-triangle" class="h-6 w-6 text-red-600 dark:text-red-500" />
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-ink-900 dark:text-ink-50" id="modal-title">
                                    Excluir Tenant
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-ink-500 dark:text-ink-400">
                                        Tem certeza que deseja excluir este tenant? Esta ação é irreversível e irá deletar TODOS os dados do tenant.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-ink-50 dark:bg-ink-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" 
                                wire:click="deleteTenant"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Excluir
                        </button>
                        <button type="button" 
                                wire:click="$set('confirmingTenantDeletion', false)"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-ink-300 dark:border-ink-700 shadow-sm px-4 py-2 bg-white dark:bg-ink-900 text-base font-medium text-ink-700 dark:text-ink-300 hover:bg-ink-50 dark:hover:bg-ink-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ink-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Script para notificações --}}
    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('tenant-deleted', (event) => {
                // Você pode usar um toast notification aqui
                if (window.toast) {
                    window.toast.success(event.message);
                } else {
                    alert(event.message);
                }
            });
            
            Livewire.on('error', (event) => {
                if (window.toast) {
                    window.toast.error(event.message);
                } else {
                    alert(event.message);
                }
            });
        });
    </script>
    @endpush
</div>