<div>
    <div class="mb-8">
        <h1 class="text-2xl font-semibold text-ink-900 dark:text-ink-50">Novo Usuário</h1>
        <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">
            Cadastre um novo usuário no sistema
        </p>
    </div>

    @if (session()->has('error'))
        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="max-w-2xl">
        <form wire:submit="save" class="space-y-6">
            <div class="bg-white dark:bg-ink-900 rounded-lg border border-ink-200 dark:border-ink-800 p-6 space-y-5">
                
                <div>
                    <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Nome *</label>
                    <input type="text" wire:model="name" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-ink-500">
                    @error('name') <p class="text-xs text-danger-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">E-mail *</label>
                    <input type="email" wire:model="email" class="w-full px-3 py-2 border rounded-lg">
                    @error('email') <p class="text-xs text-danger-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Senha *</label>
                    <input type="password" wire:model="password" class="w-full px-3 py-2 border rounded-lg">
                    @error('password') <p class="text-xs text-danger-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Confirmar Senha *</label>
                    <input type="password" wire:model="password_confirmation" class="w-full px-3 py-2 border rounded-lg">
                </div>

                <div>
                    <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">Tenant</label>
                    <select wire:model="tenant_id" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">Super Admin (Acesso ao sistema)</option>
                        @foreach($tenants as $tenant)
                            <option value="{{ $tenant->id }}">{{ $tenant->name }} ({{ $tenant->id }})</option>
                        @endforeach
                    </select>
                    @error('tenant_id') <p class="text-xs text-danger-600 mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-ink-500 mt-1">Selecione um tenant para criar um administrador do estabelecimento</p>
                </div>

                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" wire:model="is_super_admin">
                        <span class="text-sm text-ink-700 dark:text-ink-300">Super Admin (Acesso total)</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" wire:model="is_active">
                        <span class="text-sm text-ink-700 dark:text-ink-300">Usuário Ativo</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.usuarios.index') }}" wire:navigate 
                    class="px-4 py-2 border border-ink-300 rounded-lg text-ink-700 hover:bg-ink-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit" wire:loading.attr="disabled"
                    class="px-4 py-2 bg-ink-900 text-white rounded-lg hover:bg-ink-800 transition-colors disabled:opacity-50">
                    <span wire:loading.remove>Criar Usuário</span>
                    <span wire:loading>Criando...</span>
                </button>
            </div>
        </form>
    </div>
</div>