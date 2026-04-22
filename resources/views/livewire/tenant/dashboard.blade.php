<div>
    <div class="mb-8">
        <h1 class="text-2xl font-semibold text-ink-900 dark:text-ink-50">Dashboard</h1>
        <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">
            Bem-vindo, {{ auth()->user()->name }}!
        </p>
    </div>

    {{-- Botão para atualizar dados --}}
    <div class="mb-4 flex justify-end">
        <button wire:click="refreshStats" 
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 px-3 py-1.5 text-sm bg-ink-100 dark:bg-ink-800 rounded-lg hover:bg-ink-200 dark:hover:bg-ink-700 transition-colors">
            <x-ui.icon name="refresh-cw" class="size-4" wire:loading.class="animate-spin" />
            <span wire:loading.remove>Atualizar</span>
            <span wire:loading>Atualizando...</span>
        </button>
    </div>

    {{-- Cards de estatísticas --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-ui.stat-card
            label="Clientes"
            :value="$stats['total_clientes']"
            icon="users"
        />
        <x-ui.stat-card
            label="Produtos"
            :value="$stats['total_produtos']"
            icon="package"
        />
        <x-ui.stat-card
            label="Categorias"
            :value="$stats['total_categorias']"
            icon="grid"
        />
        <x-ui.stat-card
            label="Vendas Hoje"
            :value="'R$ ' . number_format($stats['vendas_hoje'], 2, ',', '.')"
            icon="shopping-cart"
        />
    </div>

    {{-- Conteúdo detalhado --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Informações do Tenant --}}
        <x-ui.card>
            <div class="flex items-start gap-4">
                <div class="size-10 rounded-lg bg-ink-100 dark:bg-ink-800 flex items-center justify-center flex-shrink-0">
                    <x-ui.icon name="building" class="size-5 text-ink-700 dark:text-ink-300" />
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-ink-900 dark:text-ink-50">
                        Informações do Estabelecimento
                    </h3>
                    <dl class="mt-3 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-ink-500">ID do Tenant</dt>
                            <dd class="font-mono text-ink-900 dark:text-ink-100">{{ tenant()->id }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-ink-500">Nome</dt>
                            <dd class="text-ink-900 dark:text-ink-100">{{ tenant()->name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-ink-500">Status</dt>
                            <dd class="capitalize text-ink-900 dark:text-ink-100">{{ tenant()->status }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-ink-500">Plano</dt>
                            <dd class="text-ink-900 dark:text-ink-100">{{ tenant()->plan->name ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-ink-500">Banco de Dados</dt>
                            <dd class="font-mono text-xs text-ink-900 dark:text-ink-100">{{ tenant()->database()->getName() }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </x-ui.card>

        {{-- Últimos Clientes --}}
        <x-ui.card>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-ink-900 dark:text-ink-50">Últimos Clientes</h3>
                <a href="{{ route('tenant.clientes.index') }}" wire:navigate class="text-xs text-ink-500 hover:text-ink-700 transition-colors">
                    Ver todos →
                </a>
            </div>
            <div class="space-y-3">
                @forelse($ultimos_clientes as $cliente)
                    <div class="flex items-center justify-between py-2 border-b border-ink-100 dark:border-ink-800 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-ink-900 dark:text-ink-100">{{ $cliente->nome }}</p>
                            <p class="text-xs text-ink-500">{{ $cliente->celular ?? $cliente->telefone ?? 'Sem telefone' }}</p>
                        </div>
                        <span class="text-xs text-ink-400">{{ $cliente->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <p class="text-sm text-ink-500 text-center py-4">
                        Nenhum cliente cadastrado ainda.
                        <a href="{{ route('tenant.clientes.create') }}" wire:navigate class="text-ink-900 underline ml-1">
                            Criar primeiro cliente
                        </a>
                    </p>
                @endforelse
            </div>
        </x-ui.card>

        {{-- Produtos em Destaque --}}
        <x-ui.card>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-ink-900 dark:text-ink-50">Produtos em Destaque</h3>
               
                <span class="text-xs text-ink-400">Em breve</span>
            </div>
            <div class="space-y-3">
                @forelse($produtos_destaque as $produto)
                    <div class="flex items-center justify-between py-2 border-b border-ink-100 dark:border-ink-800 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-ink-900 dark:text-ink-100">{{ $produto->nome }}</p>
                            <p class="text-xs text-ink-500">{{ $produto->categoria->nome ?? 'Sem categoria' }}</p>
                        </div>
                        <span class="text-sm font-semibold text-ink-900 dark:text-ink-100">
                            {{ $produto->preco_formatado }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-ink-500 text-center py-4">
                        Nenhum produto em destaque.
                        {{-- <a href="{{ route('tenant.produtos.create') }}" wire:navigate class="text-ink-900 underline ml-1">
                            Adicionar produtos
                        </a> --}}
                    </p>
                @endforelse
            </div>
        </x-ui.card>

        {{-- Ações Rápidas --}}
        <x-ui.card>
            <h3 class="text-sm font-semibold text-ink-900 dark:text-ink-50 mb-4">Ações Rápidas</h3>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('tenant.clientes.create') }}" 
                   wire:navigate
                   class="flex items-center gap-2 px-3 py-2 text-sm bg-ink-50 dark:bg-ink-800 rounded-lg hover:bg-ink-100 dark:hover:bg-ink-700 transition-colors">
                    <x-ui.icon name="user-plus" class="size-4" />
                    Novo Cliente
                </a>
                <a href="#" 
                   class="flex items-center gap-2 px-3 py-2 text-sm bg-ink-50 dark:bg-ink-800 rounded-lg hover:bg-ink-100 dark:hover:bg-ink-700 transition-colors">
                    <x-ui.icon name="package-plus" class="size-4" />
                    Novo Produto
                </a>
                <a href="#" 
                   class="flex items-center gap-2 px-3 py-2 text-sm bg-ink-50 dark:bg-ink-800 rounded-lg hover:bg-ink-100 dark:hover:bg-ink-700 transition-colors">
                    <x-ui.icon name="shopping-cart" class="size-4" />
                    Nova Venda
                </a>
                <a href="{{ route('tenant.pdv') }}" 
                   wire:navigate
                   class="flex items-center gap-2 px-3 py-2 text-sm bg-ink-900 text-white rounded-lg hover:bg-ink-800 transition-colors">
                    <x-ui.icon name="credit-card" class="size-4" />
                    Abrir PDV
                </a>
            </div>
        </x-ui.card>
    </div>
</div>