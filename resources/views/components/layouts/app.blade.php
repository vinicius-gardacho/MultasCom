@props(['title' => null])

@php
    $menu = [
        ['rota' => 'dashboard', 'ativo' => 'dashboard', 'texto' => 'Painel'],
        ['rota' => 'condutores.index', 'ativo' => 'condutores.*', 'texto' => 'Condutores'],
        ['rota' => 'veiculos.index', 'ativo' => 'veiculos.*', 'texto' => 'Veículos'],
        ['rota' => 'multas.index', 'ativo' => ['multas.index', 'multas.show'], 'texto' => 'Multas'],
        ['rota' => 'multas.inadimplentes', 'ativo' => 'multas.inadimplentes', 'texto' => 'Inadimplentes'],
        ['rota' => 'orgaos.index', 'ativo' => 'orgaos.*', 'texto' => 'Órgãos'],
        ['rota' => 'relatorios.ranking-infracoes', 'ativo' => 'relatorios.*', 'texto' => 'Relatórios'],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title.' · ' : '' }}{{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('logo.svg') }}" type="image/svg+xml">
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-slate-100 font-sans text-slate-900 antialiased">
    <header class="bg-blue-900 text-white shadow">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
            <a href="{{ route('dashboard') }}" aria-label="{{ config('app.name') }} — ir para o painel">
                <x-logo tema="escuro" />
            </a>

            <nav class="hidden items-center gap-1 lg:flex" aria-label="Menu principal">
                @foreach ($menu as $item)
                    <a href="{{ route($item['rota']) }}"
                       @class([
                           'rounded-md px-3 py-2 text-sm font-medium',
                           'bg-blue-800 text-white' => request()->routeIs($item['ativo']),
                           'text-blue-100 hover:bg-blue-800 hover:text-white' => ! request()->routeIs($item['ativo']),
                       ])>{{ $item['texto'] }}</a>
                @endforeach

                <form method="POST" action="{{ route('logout') }}" class="ml-2 border-l border-blue-700 pl-3">
                    @csrf
                    <button type="submit" class="rounded-md px-3 py-2 text-sm font-medium text-blue-100 hover:bg-blue-800 hover:text-white" title="Sair ({{ auth()->user()?->name }})">Sair</button>
                </form>
            </nav>

            <details class="relative lg:hidden">
                <summary class="cursor-pointer list-none rounded-md px-3 py-2 text-sm font-medium hover:bg-blue-800">Menu</summary>
                <nav class="absolute right-0 z-10 mt-2 w-56 rounded-md bg-white py-1 text-slate-800 shadow-lg" aria-label="Menu principal">
                    @foreach ($menu as $item)
                        <a href="{{ route($item['rota']) }}" @class(['block px-4 py-2 text-sm hover:bg-slate-100', 'font-semibold text-blue-800' => request()->routeIs($item['ativo'])])>{{ $item['texto'] }}</a>
                    @endforeach

                    <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-200">
                        @csrf
                        <button type="submit" class="block w-full px-4 py-2 text-left text-sm hover:bg-slate-100">Sair</button>
                    </form>
                </nav>
            </details>
        </div>
    </header>

    <main class="mx-auto w-full max-w-7xl flex-1 px-4 py-6 sm:px-6">
        @if ($title)
            <h1 class="mb-6 text-2xl font-semibold text-slate-900">{{ $title }}</h1>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">
                <p class="font-medium">Verifique os dados informados:</p>
                <ul class="mt-2 list-inside list-disc">
                    @foreach ($errors->all() as $erro)
                        <li>{{ $erro }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{ $slot }}
    </main>

    <footer class="border-t border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl flex-col gap-1 px-4 py-4 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <p>{{ config('app.name') }} · consulta de infrações de trânsito</p>
            @auth
                <p>
                    Usuário conectado:
                    <span class="font-medium text-slate-700">{{ auth()->user()->name }}</span>
                </p>
            @endauth
        </div>
    </footer>
</body>
</html>
