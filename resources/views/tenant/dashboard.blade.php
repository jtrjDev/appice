@extends('layouts.tenant')

@section('title', 'Dashboard')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-semibold text-ink-900 dark:text-ink-50">Dashboard</h1>
    <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">
        Bem-vindo, {{ auth()->user()->name }}!
    </p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <x-ui.stat-card label="Clientes" :value="\App\Models\Tenant\Cliente::count()" icon="users" />
    <x-ui.stat-card label="Produtos" :value="\App\Models\Tenant\Produto::count()" icon="package" />
    <x-ui.stat-card label="Categorias" :value="\App\Models\Tenant\Categoria::count()" icon="grid" />
    <x-ui.stat-card label="Vendas Hoje" value="0" icon="shopping-cart" />
</div>

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
                </dl>
            </div>
        </div>
    </x-ui.card>

    {{-- Últimos Clientes --}}
    <x-ui.card>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-ink-900 dark:text-ink-50">Últimos Clientes</h3>
            <a href="{{ route('tenant.clientes.index') }}" wire:navigate class="text-xs text-ink-500 hover:text-ink-700">
                Ver todos →
            </a>
        </div>
        <div class="space-y-3">
            @forelse(\App\Models\Tenant\Cliente::latest()->take(5)->get() as $cliente)
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-ink-900 dark:text-ink-100">{{ $cliente->nome }}</p>
                        <p class="text-xs text-ink-500">{{ $cliente->celular ?? $cliente->telefone }}</p>
                    </div>
                    <span class="text-xs text-ink-400">{{ $cliente->created_at->diffForHumans() }}</span>
                </div>
            @empty
                <p class="text-sm text-ink-500 text-center py-4">Nenhum cliente cadastrado ainda.</p>
            @endforelse
        </div>
    </x-ui.card>
</div>
@endsection