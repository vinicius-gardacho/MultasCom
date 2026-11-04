@props(['code', 'title', 'message'])

<x-layouts.guest :title="$title">
    <p class="text-sm font-semibold text-blue-700">Erro {{ $code }}</p>
    <h1 class="mt-1 text-lg font-semibold text-slate-900">{{ $title }}</h1>
    <p class="mt-2 text-sm text-slate-600">{{ $message }}</p>

    <a href="{{ url('/') }}" class="btn btn-primary mt-6 w-full">Voltar ao painel</a>
</x-layouts.guest>
