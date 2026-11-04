@props(['status'])

@php
    // Cores apenas para exibição; os status vêm da tabela status_multa.
    $cor = match ($status) {
        'Paga' => 'bg-green-100 text-green-800',
        'Pendente' => 'bg-amber-100 text-amber-800',
        'Vencida' => 'bg-red-100 text-red-800',
        'Contestada' => 'bg-blue-100 text-blue-800',
        default => 'bg-slate-100 text-slate-700',
    };
@endphp

<span {{ $attributes->class(['inline-flex rounded-full px-2 py-0.5 text-xs font-medium whitespace-nowrap', $cor]) }}>{{ $status }}</span>
