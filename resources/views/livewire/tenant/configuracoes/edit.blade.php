<div>
    <div class="mb-8">
        <h1 class="text-2xl font-semibold text-ink-900 dark:text-ink-50">Editar Configuração</h1>
        <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">
            Atualize os dados da empresa: {{ $razao_social }}
        </p>
    </div>

    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-lg">
            <p class="text-sm text-red-600 dark:text-red-400">{{ session('error') }}</p>
        </div>
    @endif

    <form wire:submit="update">
        <!-- Mesmo conteúdo do create.blade.php -->
        <div class="bg-white dark:bg-ink-900 rounded-lg border border-ink-200 dark:border-ink-800 overflow-hidden">
            {{-- Abas (igual ao create) --}}
            <div class="border-b border-ink-200 dark:border-ink-800 bg-ink-50 dark:bg-ink-800/50">
                <nav class="flex gap-1 px-4 overflow-x-auto">
                    <button type="button" wire:click="$set('activeTab', 'empresa')"
                        class="px-4 py-3 text-sm font-medium transition-colors {{ $activeTab == 'empresa' ? 'border-b-2 border-ink-900 text-ink-900 dark:text-ink-50' : 'text-ink-500 hover:text-ink-700' }}">
                        🏢 Empresa
                    </button>
                    <button type="button" wire:click="$set('activeTab', 'endereco')"
                        class="px-4 py-3 text-sm font-medium transition-colors {{ $activeTab == 'endereco' ? 'border-b-2 border-ink-900 text-ink-900 dark:text-ink-50' : 'text-ink-500 hover:text-ink-700' }}">
                        📍 Endereço
                    </button>
                    <button type="button" wire:click="$set('activeTab', 'contato')"
                        class="px-4 py-3 text-sm font-medium transition-colors {{ $activeTab == 'contato' ? 'border-b-2 border-ink-900 text-ink-900 dark:text-ink-50' : 'text-ink-500 hover:text-ink-700' }}">
                        📞 Contato
                    </button>
                    <button type="button" wire:click="$set('activeTab', 'logo')"
                        class="px-4 py-3 text-sm font-medium transition-colors {{ $activeTab == 'logo' ? 'border-b-2 border-ink-900 text-ink-900 dark:text-ink-50' : 'text-ink-500 hover:text-ink-700' }}">
                        🖼️ Logo
                    </button>
                    <button type="button" wire:click="$set('activeTab', 'nf')"
                        class="px-4 py-3 text-sm font-medium transition-colors {{ $activeTab == 'nf' ? 'border-b-2 border-ink-900 text-ink-900 dark:text-ink-50' : 'text-ink-500 hover:text-ink-700' }}">
                        📄 Nota Fiscal
                    </button>
                    <button type="button" wire:click="$set('activeTab', 'certificado')"
                        class="px-4 py-3 text-sm font-medium transition-colors {{ $activeTab == 'certificado' ? 'border-b-2 border-ink-900 text-ink-900 dark:text-ink-50' : 'text-ink-500 hover:text-ink-700' }}">
                        🔐 Certificado
                    </button>
                    <button type="button" wire:click="$set('activeTab', 'cupom')"
                        class="px-4 py-3 text-sm font-medium transition-colors {{ $activeTab == 'cupom' ? 'border-b-2 border-ink-900 text-ink-900 dark:text-ink-50' : 'text-ink-500 hover:text-ink-700' }}">
                        🧾 Cupom Fiscal
                    </button>
                    <button type="button" wire:click="$set('activeTab', 'fiscal')"
                        class="px-4 py-3 text-sm font-medium transition-colors {{ $activeTab == 'fiscal' ? 'border-b-2 border-ink-900 text-ink-900 dark:text-ink-50' : 'text-ink-500 hover:text-ink-700' }}">
                        ⚖️ Regime Fiscal
                    </button>
                    <button type="button" wire:click="$set('activeTab', 'webhook')"
                        class="px-4 py-3 text-sm font-medium transition-colors {{ $activeTab == 'webhook' ? 'border-b-2 border-ink-900 text-ink-900 dark:text-ink-50' : 'text-ink-500 hover:text-ink-700' }}">
                        🔗 Webhooks
                    </button>
                </nav>
            </div>

            <div class="p-6 space-y-6">
                {{-- Conteúdo das abas (mesmo do create) --}}
                @include('livewire.tenant.configuracoes.partials.form-empresa')
                @include('livewire.tenant.configuracoes.partials.form-endereco')
                @include('livewire.tenant.configuracoes.partials.form-contato')
                @include('livewire.tenant.configuracoes.partials.form-logo')
                @include('livewire.tenant.configuracoes.partials.form-nf')
                @include('livewire.tenant.configuracoes.partials.form-certificado')
                @include('livewire.tenant.configuracoes.partials.form-cupom')
                @include('livewire.tenant.configuracoes.partials.form-fiscal')
                @include('livewire.tenant.configuracoes.partials.form-webhook')
            </div>
        </div>

        {{-- Botões --}}
        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('tenant.configuracoes.index') }}" wire:navigate
                class="px-4 py-2 border border-ink-300 dark:border-ink-700 rounded-lg text-ink-700 dark:text-ink-300 hover:bg-ink-50 dark:hover:bg-ink-800 transition-colors">
                Cancelar
            </a>
            <button type="submit" wire:loading.attr="disabled"
                class="px-4 py-2 bg-ink-900 text-white rounded-lg hover:bg-ink-800 transition-colors disabled:opacity-50">
                <span wire:loading.remove>Atualizar Configuração</span>
                <span wire:loading>Atualizando...</span>
            </button>
        </div>
    </form>
</div>