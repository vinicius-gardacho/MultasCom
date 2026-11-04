@php
    $relatorios = [
        'relatorios.ranking-infracoes' => 'Infrações mais cometidas',
        'relatorios.veiculos-mais-multados' => 'Veículos mais multados',
        'relatorios.cnh-proximas-vencer' => 'CNHs a vencer',
    ];
@endphp

<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <nav class="flex flex-wrap gap-2" aria-label="Relatórios">
        @foreach ($relatorios as $rota => $texto)
            <a href="{{ route($rota) }}"
               @class([
                   'rounded-full px-4 py-1.5 text-sm font-medium',
                   'bg-blue-700 text-white' => request()->routeIs($rota),
                   'border border-slate-300 bg-white text-slate-700 hover:bg-slate-50' => ! request()->routeIs($rota),
               ])>{{ $texto }}</a>
        @endforeach
    </nav>

    @foreach (array_keys($relatorios) as $rota)
        @if (request()->routeIs($rota))
            <a href="{{ route($rota.'.pdf') }}" class="btn btn-secondary" download>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4" aria-hidden="true">
                    <path d="M10.75 2.75a.75.75 0 0 0-1.5 0v8.614L6.295 8.235a.75.75 0 1 0-1.09 1.03l4.25 4.5a.75.75 0 0 0 1.09 0l4.25-4.5a.75.75 0 0 0-1.09-1.03l-2.955 3.129V2.75Z" />
                    <path d="M3.5 12.75a.75.75 0 0 0-1.5 0v2.5A2.75 2.75 0 0 0 4.75 18h10.5A2.75 2.75 0 0 0 18 15.25v-2.5a.75.75 0 0 0-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5Z" />
                </svg>
                Exportar PDF
            </a>
        @endif
    @endforeach
</div>
