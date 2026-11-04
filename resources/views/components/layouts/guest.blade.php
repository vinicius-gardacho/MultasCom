@props(['title' => null])

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
<body class="flex min-h-screen items-center justify-center bg-slate-100 px-4 font-sans text-slate-900 antialiased">
    <main class="w-full max-w-sm">
        <div class="mb-6 flex flex-col items-center gap-2 text-center">
            <x-logo tamanho="h-14 w-14" texto="text-3xl" />
            <p class="text-sm text-slate-500">Consulta de infrações de trânsito</p>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            {{ $slot }}
        </div>
    </main>
</body>
</html>
