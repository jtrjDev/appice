<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Painel Admin') · {{ config('app.name') }}</title>

    {{-- Anti-flash simplificado --}}
    <script>
        (function() {
            const saved = localStorage.getItem('theme');
            const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (saved === 'dark' || (!saved && systemDark)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="font-sans antialiased min-h-screen bg-white dark:bg-ink-950">

    {{-- ============ TOPBAR ============ --}}
    <header class="sticky top-0 z-40 bg-white/80 dark:bg-ink-950/80 backdrop-blur-lg border-b border-ink-200 dark:border-ink-800">
        <div class="max-w-[1400px] mx-auto px-6">
            <div class="flex items-center h-14 gap-6">

                {{-- Logo + Nome --}}
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 group">
                    <div class="size-7 rounded-md bg-ink-900 dark:bg-white flex items-center justify-center">
                        <x-ui.icon name="spark" class="size-4 text-white dark:text-ink-900" />
                    </div>
                    <span class="font-semibold text-sm text-ink-900 dark:text-ink-50 tracking-tight">
                        {{ config('app.name') }}
                    </span>
                    <span class="text-xs px-1.5 py-0.5 rounded font-medium bg-ink-100 dark:bg-ink-800 text-ink-600 dark:text-ink-400">
                        admin
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

                    @php $dash = $navLink('admin.dashboard', 'Dashboard', 'dashboard'); @endphp
                    <a href="{{ route('admin.dashboard') }}" class="{{ $dash['class'] }}">
                        <x-ui.icon :name="$dash['icon']" class="size-4" />
                        {{ $dash['label'] }}
                    </a>

                    @php
                    $ten = $navLink('admin.tenants.*', 'Tenants', 'building', false);
                    @endphp
                    <a href="{{ route('admin.tenants.index') }}"
                        wire:navigate
                        class="{{ $ten['class'] }}">
                        <x-ui.icon :name="$ten['icon']" class="size-4" />
                        {{ $ten['label'] }}
                    </a>

                    @php $plan = $navLink('admin.plans.*', 'Planos', 'package', true); @endphp
                    <a href="#" class="{{ $plan['class'] }}">
                        <x-ui.icon :name="$plan['icon']" class="size-4" />
                        {{ $plan['label'] }}
                    </a>

                    @php $usr = $navLink('admin.usuarios.*', 'Usuários', 'users', false); @endphp
                    <a href="{{ route('admin.usuarios.index') }}" wire:navigate class="{{ $usr['class'] }}">
                        <x-ui.icon :name="$usr['icon']" class="size-4" />
                        {{ $usr['label'] }}
                    </a>

                </nav>

                {{-- Espaço flexível --}}
                <div class="flex-1"></div>

                {{-- Ações direita: toggle de tema + user dropdown --}}
                <div class="flex items-center gap-2">

                    {{-- Toggle dark mode --}}
                    <button
                        type="button"
                        onclick="window.themeManager.toggle()"
                        class="size-8 inline-flex items-center justify-center rounded-md text-ink-600 hover:text-ink-900 hover:bg-ink-100 dark:text-ink-400 dark:hover:text-ink-100 dark:hover:bg-ink-800 transition-colors"
                        title="Alternar tema">
                        <x-ui.icon name="sun" class="size-4 block dark:hidden" />
                        <x-ui.icon name="moon" class="size-4 hidden dark:block" />
                    </button>

                    {{-- User dropdown (Alpine.js) --}}
                    <div x-data="{ open: false }" class="relative">
                        <button
                            @click="open = !open"
                            @click.outside="open = false"
                            class="inline-flex items-center gap-2 h-8 pl-1 pr-2 rounded-md hover:bg-ink-100 dark:hover:bg-ink-800 transition-colors">
                            {{-- Avatar com iniciais --}}
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

                        {{-- Dropdown menu --}}
                        <div
                            x-show="open"
                            x-cloak
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
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
                                    Minha conta
                                </a>
                                <a href="#" class="flex items-center gap-2 px-3 py-1.5 text-sm text-ink-700 dark:text-ink-300 hover:bg-ink-50 dark:hover:bg-ink-800">
                                    <x-ui.icon name="settings" class="size-4" />
                                    Configurações
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

    {{-- ============ CONTENT ============ --}}
    <main class="max-w-[1400px] mx-auto px-6 py-8">

        {{-- Flash messages --}}
        @if (session('success'))
        <div class="mb-4 flex items-start gap-3 p-3 rounded-lg border border-success-500/20 bg-success-50 dark:bg-success-500/5">
            <x-ui.icon name="check-circle" class="size-4 text-success-600 mt-0.5 flex-shrink-0" />
            <p class="text-sm text-success-600 dark:text-success-500">{{ session('success') }}</p>
        </div>
        @endif

        @if (session('error'))
        <div class="mb-4 flex items-start gap-3 p-3 rounded-lg border border-danger-500/20 bg-danger-50 dark:bg-danger-500/5">
            <x-ui.icon name="alert-circle" class="size-4 text-danger-600 mt-0.5 flex-shrink-0" />
            <p class="text-sm text-danger-600 dark:text-danger-500">{{ session('error') }}</p>
        </div>
        @endif

        @yield('content')
        {{ $slot ?? '' }}
    </main>

    @livewireScripts
</body>

</html>