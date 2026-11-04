@props(['tema' => 'claro', 'tamanho' => 'h-9 w-9', 'texto' => 'text-lg'])

{{-- Logo do MultasCom: multa (documento) com alerta. tema "escuro" = texto branco, para fundos escuros. --}}
<span {{ $attributes->class('inline-flex items-center gap-2.5') }}>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="{{ $tamanho }} shrink-0" aria-hidden="true">
        <rect width="48" height="48" rx="11" fill="#1e3a8a" @if ($tema === 'escuro') stroke="#93c5fd" stroke-opacity="0.5" stroke-width="1.5" @endif/>
        <path d="M14 9h14l8 8v21a2 2 0 0 1-2 2H14a2 2 0 0 1-2-2V11a2 2 0 0 1 2-2z" fill="#ffffff"/>
        <path d="M28 9v6a2 2 0 0 0 2 2h6z" fill="#bfdbfe"/>
        <rect x="16" y="16" width="9" height="2.4" rx="1.2" fill="#1e3a8a" fill-opacity="0.35"/>
        <rect x="16" y="21.5" width="15" height="2.4" rx="1.2" fill="#1e3a8a" fill-opacity="0.35"/>
        <rect x="16" y="27" width="8" height="2.4" rx="1.2" fill="#1e3a8a" fill-opacity="0.35"/>
        <circle cx="34" cy="34" r="9.5" fill="#f59e0b" stroke="#1e3a8a" stroke-width="2.5"/>
        <rect x="32.8" y="28.2" width="2.4" height="7.2" rx="1.2" fill="#1e3a8a"/>
        <circle cx="34" cy="38.6" r="1.4" fill="#1e3a8a"/>
    </svg>
    <span @class([$texto, 'font-semibold tracking-tight', 'text-white' => $tema === 'escuro', 'text-blue-900' => $tema !== 'escuro'])>Multas<span class="text-amber-500">Com</span></span>
</span>
