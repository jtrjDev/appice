<div>
    <div class="mb-8">
        <h1 class="text-2xl font-semibold text-ink-900 dark:text-ink-50">Criar Novo Tenant</h1>
        <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">
            Preencha os dados abaixo para criar um novo tenant
        </p>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 p-4 rounded-lg bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20">
            <p class="text-sm text-green-600 dark:text-green-400">{{ session('success') }}</p>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 rounded-lg bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20">
            <p class="text-sm text-red-600 dark:text-red-400">{{ session('error') }}</p>
        </div>
    @endif

    <div class="max-w-2xl">
        <form wire:submit="save" class="space-y-6">
            <div class="bg-white dark:bg-ink-900 rounded-lg border border-ink-200 dark:border-ink-800 p-6 space-y-5">
                
                <h2 class="text-lg font-semibold text-ink-900 dark:text-ink-50 pb-2 border-b border-ink-200 dark:border-ink-800">
                    Informações do Tenant
                </h2>

                {{-- ID do Tenant --}}
                <div>
                    <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">
                        ID do Tenant *
                    </label>
                    <input type="text" 
                           wire:model="tenant_id"
                           class="w-full px-3 py-2 border border-ink-300 dark:border-ink-700 rounded-lg bg-white dark:bg-ink-800 text-ink-900 dark:text-ink-100 focus:ring-2 focus:ring-ink-500 focus:border-transparent"
                           placeholder="ex: minha-empresa">
                    @error('tenant_id') 
                        <p class="mt-1 text-xs text-danger-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-ink-500 dark:text-ink-400">
                        Apenas letras minúsculas, números e hífens. Ex: "minha-empresa"
                    </p>
                </div>

                {{-- Nome do Tenant --}}
                <div>
                    <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">
                        Nome do Tenant *
                    </label>
                    <input type="text" 
                           wire:model="name"
                           class="w-full px-3 py-2 border border-ink-300 dark:border-ink-700 rounded-lg bg-white dark:bg-ink-800 text-ink-900 dark:text-ink-100 focus:ring-2 focus:ring-ink-500 focus:border-transparent">
                    @error('name') 
                        <p class="mt-1 text-xs text-danger-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- E-mail do Tenant --}}
                <div>
                    <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">
                        E-mail do Tenant *
                    </label>
                    <input type="email" 
                           wire:model="email"
                           class="w-full px-3 py-2 border border-ink-300 dark:border-ink-700 rounded-lg bg-white dark:bg-ink-800 text-ink-900 dark:text-ink-100 focus:ring-2 focus:ring-ink-500 focus:border-transparent">
                    @error('email') 
                        <p class="mt-1 text-xs text-danger-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Plano e Status --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">
                            Plano *
                        </label>
                        <select wire:model="plan_id" 
                                class="w-full px-3 py-2 border border-ink-300 dark:border-ink-700 rounded-lg bg-white dark:bg-ink-800 text-ink-900 dark:text-ink-100 focus:ring-2 focus:ring-ink-500 focus:border-transparent">
                            <option value="">Selecione um plano</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}">{{ $plan->name }} - {{ $plan->price_formatted }}</option>
                            @endforeach
                        </select>
                        @error('plan_id') 
                            <p class="mt-1 text-xs text-danger-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">
                            Status *
                        </label>
                        <select wire:model="status" 
                                class="w-full px-3 py-2 border border-ink-300 dark:border-ink-700 rounded-lg bg-white dark:bg-ink-800 text-ink-900 dark:text-ink-100 focus:ring-2 focus:ring-ink-500 focus:border-transparent">
                            <option value="active">Ativo</option>
                            <option value="trial">Trial</option>
                            <option value="inactive">Inativo</option>
                        </select>
                        @error('status') 
                            <p class="mt-1 text-xs text-danger-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Dias de Trial --}}
                <div>
                    <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">
                        Dias de Trial
                    </label>
                    <input type="number" 
                           wire:model="trial_days"
                           class="w-full px-3 py-2 border border-ink-300 dark:border-ink-700 rounded-lg bg-white dark:bg-ink-800 text-ink-900 dark:text-ink-100 focus:ring-2 focus:ring-ink-500 focus:border-transparent">
                    @error('trial_days') 
                        <p class="mt-1 text-xs text-danger-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-ink-500 dark:text-ink-400">
                        Deixe 0 para sem trial
                    </p>
                </div>

                <div class="border-t border-ink-200 dark:border-ink-800 pt-5">
                    <h2 class="text-lg font-semibold text-ink-900 dark:text-ink-50 pb-2">
                        Usuário Administrador
                    </h2>
                    <p class="text-sm text-ink-500 dark:text-ink-400 mb-4">
                        Este será o primeiro usuário do tenant (acesso ao /app)
                    </p>

                    {{-- Nome do Admin --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">
                            Nome do Admin *
                        </label>
                        <input type="text" 
                               wire:model="admin_name"
                               class="w-full px-3 py-2 border border-ink-300 dark:border-ink-700 rounded-lg bg-white dark:bg-ink-800 text-ink-900 dark:text-ink-100 focus:ring-2 focus:ring-ink-500 focus:border-transparent">
                        @error('admin_name') 
                            <p class="mt-1 text-xs text-danger-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- E-mail do Admin --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">
                            E-mail do Admin *
                        </label>
                        <input type="email" 
                               wire:model="admin_email"
                               class="w-full px-3 py-2 border border-ink-300 dark:border-ink-700 rounded-lg bg-white dark:bg-ink-800 text-ink-900 dark:text-ink-100 focus:ring-2 focus:ring-ink-500 focus:border-transparent">
                        @error('admin_email') 
                            <p class="mt-1 text-xs text-danger-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Senha do Admin --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">
                            Senha do Admin *
                        </label>
                        <input type="password" 
                               wire:model="admin_password"
                               class="w-full px-3 py-2 border border-ink-300 dark:border-ink-700 rounded-lg bg-white dark:bg-ink-800 text-ink-900 dark:text-ink-100 focus:ring-2 focus:ring-ink-500 focus:border-transparent">
                        @error('admin_password') 
                            <p class="mt-1 text-xs text-danger-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirmação de Senha --}}
                    <div>
                        <label class="block text-sm font-medium text-ink-700 dark:text-ink-300 mb-1">
                            Confirmar Senha *
                        </label>
                        <input type="password" 
                               wire:model="admin_password_confirmation"
                               class="w-full px-3 py-2 border border-ink-300 dark:border-ink-700 rounded-lg bg-white dark:bg-ink-800 text-ink-900 dark:text-ink-100 focus:ring-2 focus:ring-ink-500 focus:border-transparent">
                        @error('admin_password_confirmation') 
                            <p class="mt-1 text-xs text-danger-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Botões --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.tenants.index') }}" 
                   wire:navigate
                   class="px-4 py-2 border border-ink-300 dark:border-ink-700 rounded-lg text-ink-700 dark:text-ink-300 hover:bg-ink-50 dark:hover:bg-ink-800 transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        wire:loading.attr="disabled"
                        class="px-4 py-2 bg-ink-900 text-white rounded-lg hover:bg-ink-800 transition-colors disabled:opacity-50">
                    <span wire:loading.remove>Criar Tenant</span>
                    <span wire:loading>Criando...</span>
                </button>
            </div>
        </form>
    </div>
</div>