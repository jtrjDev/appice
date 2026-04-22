<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', tenant()->name) · {{ config('app.name') }}</title>

    <script>
        (function() {
            const saved = localStorage.getItem('theme');
            const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (saved === 'dark' || (!saved && systemDark)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

@php
    $manifestPath = public_path('build/manifest.json');
    if (file_exists($manifestPath)) {
        $manifest = json_decode(file_get_contents($manifestPath), true);
        $cssFile = $manifest['resources/css/app.css']['file'] ?? 'app-B3ehL459.css';
        $jsFile = $manifest['resources/js/app.js']['file'] ?? 'app-BjoUdjsi.js';
    } else {
        $cssFile = 'app-B3ehL459.css';
        $jsFile = 'app-BjoUdjsi.js';
    }
@endphp

<link rel="stylesheet" href="{{ url('/build/' . $cssFile) }}">
<script src="{{ url('/build/' . $jsFile) }}" defer></script>
    @livewireStyles
</head>
<body class="font-sans antialiased min-h-screen bg-white dark:bg-ink-950">

    {{-- TOPBAR --}}
    <header class="sticky top-0 z-40 bg-white/80 dark:bg-ink-950/80 backdrop-blur-lg border-b border-ink-200 dark:border-ink-800">
        <div class="max-w-[1400px] mx-auto px-6">
            <div class="flex items-center h-14 gap-6">

                {{-- Logo + Nome do Tenant --}}
                <a href="{{ route('tenant.dashboard') }}" wire:navigate class="flex items-center gap-2 group">
                    <div class="size-7 rounded-md bg-gradient-to-br from-ink-700 to-ink-900 dark:from-ink-200 dark:to-ink-50 flex items-center justify-center">
                        <x-ui.icon name="ice-cream" class="size-4 text-white dark:text-ink-900" />
                    </div>
                    <span class="font-semibold text-sm text-ink-900 dark:text-ink-50 tracking-tight">
                        {{ tenant()->name }}
                    </span>
                </a>

                {{-- Divisor --}}
                <div class="h-5 w-px bg-ink-200 dark:bg-ink-800"></div>

                {{-- Navegação --}}
                <nav class="flex items-center gap-1">
                    @php
                        $navLink = function($route, $label, $icon, $disabled = false) {
                            $active = !$disabled && request()->routeIs($route);
                            $base = 'inline-flex items-center gap-2 h-8 px-3 text-sm font-medium rounded-md transition-colors';
                            if ($disabled) {
                                return ['class' => $base . ' text-ink-400 dark:text-ink-600 cursor-not-allowed', 'label' => $label, 'icon' => $icon];
                            }
                            return [
                                'class' => $base . ($active
                                    ? ' bg-ink-100 text-ink-900 dark:bg-ink-800 dark:text-ink-50'
                                    : ' text-ink-600 hover:text-ink-900 hover:bg-ink-50 dark:text-ink-400 dark:hover:text-ink-100 dark:hover:bg-ink-800/50'),
                                'label' => $label,
                                'icon' => $icon,
                            ];
                        };
                    @endphp

                    @php $dash = $navLink('tenant.dashboard', 'Dashboard', 'dashboard'); @endphp
                    <a href="{{ route('tenant.dashboard') }}" wire:navigate class="{{ $dash['class'] }}">
                        <x-ui.icon :name="$dash['icon']" class="size-4" />
                        {{ $dash['label'] }}
                    </a>

                    @php $produtos = $navLink('tenant.produtos.*', 'Produtos', 'package'); @endphp
                    <a href="{{ route('tenant.produtos.index') }}" wire:navigate class="{{ $produtos['class'] }}">
                        <x-ui.icon :name="$produtos['icon']" class="size-4" />
                        {{ $produtos['label'] }}
                    </a>
                   
                    @php $caixa = $navLink('tenant.caixa', 'CAIXA', 'table'); @endphp
                    <a href="{{ route('tenant.caixa') }}" wire:navigate class="{{ $caixa['class'] }}">
                        <x-ui.icon :name="$caixa['icon']" class="size-4" />
                        {{ $caixa['label'] }}
                    </a>
                    
                    @php $pdv = $navLink('tenant.pdv', 'PDV', 'shopping-cart'); @endphp
                    <a href="{{ route('tenant.pdv') }}" wire:navigate class="{{ $pdv['class'] }}">
                        <x-ui.icon :name="$pdv['icon']" class="size-4" />
                        {{ $pdv['label'] }}
                    </a>
                    
                    @php $mesa = $navLink('tenant.mesas', 'MESA', 'table'); @endphp
                    <a href="{{ route('tenant.mesas') }}" wire:navigate class="{{ $mesa['class'] }}">
                        <x-ui.icon :name="$mesa['icon']" class="size-4" />
                        {{ $mesa['label'] }}
                    </a>

                    @php $clientes = $navLink('tenant.clientes.*', 'Clientes', 'users'); @endphp
                    <a href="{{ route('tenant.clientes.index') }}" wire:navigate class="{{ $clientes['class'] }}">
                        <x-ui.icon :name="$clientes['icon']" class="size-4" />
                        {{ $clientes['label'] }}
                    </a>

                    {{-- CONFIGURAÇÕES --}}
                    @php $config = $navLink('tenant.configuracoes.*', 'Configurações', 'settings'); @endphp
                    <a href="{{ route('tenant.configuracoes.index') }}" wire:navigate class="{{ $config['class'] }}">
                        <x-ui.icon :name="$config['icon']" class="size-4" />
                        {{ $config['label'] }}
                    </a>
                </nav>

                {{-- Espaço flexível --}}
                <div class="flex-1"></div>

                {{-- Ações direita --}}
                <div class="flex items-center gap-2">
                    {{-- Toggle dark mode --}}
                    <button type="button" onclick="window.themeManager.toggle()" 
                            class="size-8 inline-flex items-center justify-center rounded-md text-ink-600 hover:text-ink-900 hover:bg-ink-100 dark:text-ink-400 dark:hover:text-ink-100 dark:hover:bg-ink-800 transition-colors"
                            title="Alternar tema">
                        <x-ui.icon name="sun" class="size-4 block dark:hidden" />
                        <x-ui.icon name="moon" class="size-4 hidden dark:block" />
                    </button>

                    {{-- User dropdown --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.outside="open = false"
                                class="inline-flex items-center gap-2 h-8 pl-1 pr-2 rounded-md hover:bg-ink-100 dark:hover:bg-ink-800 transition-colors">
                            <div class="size-6 rounded-full bg-gradient-to-br from-ink-700 to-ink-900 dark:from-ink-200 dark:to-ink-50 flex items-center justify-center">
                                <span class="text-[10px] font-semibold text-white dark:text-ink-900">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                </span>
                            </div>
                            <span class="text-sm font-medium text-ink-900 dark:text-ink-100 hidden md:inline">
                                {{ explode(' ', auth()->user()->name)[0] }}
                            </span>
                            <x-ui.icon name="chevron-down" class="size-3 text-ink-500" />
                        </button>

                        <div x-show="open" x-cloak x-transition
                             class="absolute right-0 mt-2 w-56 origin-top-right rounded-lg bg-white dark:bg-ink-900 border border-ink-200 dark:border-ink-800 shadow-soft-md py-1 z-50">
                            <div class="px-3 py-2 border-b border-ink-100 dark:border-ink-800">
                                <p class="text-sm font-medium text-ink-900 dark:text-ink-50 truncate">
                                    {{ auth()->user()->name }}
                                </p>
                                <p class="text-xs text-ink-500 dark:text-ink-400 truncate">
                                    {{ auth()->user()->email }}
                                </p>
                            </div>
                            <div class="py-1">
                                <a href="#" class="flex items-center gap-2 px-3 py-1.5 text-sm text-ink-700 dark:text-ink-300 hover:bg-ink-50 dark:hover:bg-ink-800">
                                    <x-ui.icon name="user" class="size-4" />
                                    Perfil
                                </a>
                                <a href="{{ route('tenant.configuracoes.index') }}" wire:navigate class="flex items-center gap-2 px-3 py-1.5 text-sm text-ink-700 dark:text-ink-300 hover:bg-ink-50 dark:hover:bg-ink-800">
                                    <x-ui.icon name="settings" class="size-4" />
                                    Configurações da Empresa
                                </a>
                            </div>
                            <div class="py-1 border-t border-ink-100 dark:border-ink-800">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-1.5 text-sm text-ink-700 dark:text-ink-300 hover:bg-ink-50 dark:hover:bg-ink-800">
                                        <x-ui.icon name="log-out" class="size-4" />
                                        Sair
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- MAIN CONTENT --}}
    <main class="max-w-[1400px] mx-auto px-6 py-8">
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
        {{ $slot ?? '' }}
    </main>

    @livewireScripts
    
    <script>
        document.addEventListener('livewire:navigated', () => {
            // Re-inicializar componentes após navegação
        });
    </script>
</body>
</html>