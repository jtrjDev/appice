@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    {{-- Header da página --}}
    <div class="mb-8 flex items-end justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-ink-900 dark:text-ink-50 tracking-tight">Dashboard</h1>
            <p class="mt-1 text-sm text-ink-500 dark:text-ink-400">
                Visão geral do seu SaaS
            </p>
        </div>
        <div class="text-xs text-ink-500 dark:text-ink-400 tabular-nums">
            {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    {{-- Cards de estatísticas --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-ui.stat-card
            label="Tenants"
            :value="$stats['total_tenants']"
            icon="building"
        />
        <x-ui.stat-card
            label="Ativos"
            :value="$stats['active_tenants']"
            icon="check-circle"
        />
        <x-ui.stat-card
            label="Planos"
            :value="$stats['total_plans']"
            icon="package"
        />
        <x-ui.stat-card
            label="Planos Ativos"
            :value="$stats['active_plans']"
            icon="spark"
        />
    </div>

    {{-- Seção de bem-vindo --}}
    <x-ui.card>
        <div class="flex items-start gap-4">
            <div class="size-10 rounded-lg bg-ink-100 dark:bg-ink-800 flex items-center justify-center flex-shrink-0">
                <x-ui.icon name="spark" class="size-5 text-ink-700 dark:text-ink-300" />
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-ink-900 dark:text-ink-50">
                    Bem-vindo, {{ explode(' ', auth()->user()->name)[0] }}
                </h3>
                <p class="mt-1 text-sm text-ink-600 dark:text-ink-400 max-w-2xl">
                    Este é o seu painel administrativo. Em breve você poderá gerenciar tenants, planos e usuários diretamente por aqui. Os links do menu serão ativados conforme formos construindo os CRUDs.
                </p>
                <div class="mt-4 flex gap-2">
                    <x-ui.button variant="primary" size="sm" icon="plus">
                        Criar tenant
                    </x-ui.button>
                    <x-ui.button variant="secondary" size="sm">
                        Ver documentação
                    </x-ui.button>
                </div>
            </div>
        </div>
    </x-ui.card>
@endsection