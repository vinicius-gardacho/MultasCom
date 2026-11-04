<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Multas
        </h2>
    </x-slot>

    @php
        $dinheiro = fn ($valor) => 'R$ '.number_format((float) $valor, 2, ',', '.');
        $cpf = fn ($valor) => preg_replace('/^(\d{3})(\d{3})(\d{3})(\d{2})$/', '$1.$2.$3-$4', (string) $valor);
        $corStatus = fn ($status) => match (mb_strtolower((string) $status)) {
            'paga', 'pago' => 'bg-green-100 text-green-800',
            'pendente' => 'bg-yellow-100 text-yellow-800',
            'vencida' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
        $temFiltro = collect($filtros)->filter(fn ($valor) => filled($valor))->isNotEmpty();
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 sm:rounded-lg">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $erro)
                            <li>{{ $erro }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Filtros --}}
            <form method="GET" action="{{ route('multas.index') }}" class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
                    <div class="lg:col-span-2">
                        <label for="q" class="block text-sm font-medium text-gray-700">Busca</label>
                        <input id="q" name="q" type="text" value="{{ $filtros['q'] ?? '' }}"
                               placeholder="Condutor, CPF, placa ou infração"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select id="status" name="status"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todos</option>
                            @foreach ($statusDisponiveis as $status)
                                <option value="{{ $status }}" @selected(($filtros['status'] ?? null) === $status)>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="orgao" class="block text-sm font-medium text-gray-700">Órgão</label>
                        <select id="orgao" name="orgao"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todos</option>
                            @foreach ($orgaos as $orgao)
                                <option value="{{ $orgao->id_orgao }}" @selected((string) ($filtros['orgao'] ?? '') === (string) $orgao->id_orgao)>
                                    {{ $orgao->sigla }} — {{ $orgao->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2 sm:col-span-2 lg:col-span-1">
                        <div>
                            <label for="data_inicio" class="block text-sm font-medium text-gray-700">De</label>
                            <input id="data_inicio" name="data_inicio" type="date" value="{{ $filtros['data_inicio'] ?? '' }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="data_fim" class="block text-sm font-medium text-gray-700">Até</label>
                            <input id="data_fim" name="data_fim" type="date" value="{{ $filtros['data_fim'] ?? '' }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex items-center gap-3">
                    <x-primary-button>Filtrar</x-primary-button>

                    @if ($temFiltro)
                        <a href="{{ route('multas.index') }}" class="text-sm text-gray-600 underline hover:text-gray-900">
                            Limpar filtros
                        </a>
                    @endif
                </div>
            </form>

            {{-- Resumo --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Multas encontradas</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($multas->total(), 0, ',', '.') }}</div>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Valor total{{ $temFiltro ? ' (com filtros)' : '' }}</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $dinheiro($valorTotal) }}</div>
                </div>
            </div>

            {{-- Listagem --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="px-4 py-3">Data/hora</th>
                                <th class="px-4 py-3">Condutor</th>
                                <th class="px-4 py-3">Placa</th>
                                <th class="px-4 py-3">Infração</th>
                                <th class="px-4 py-3 text-right">Pontos</th>
                                <th class="px-4 py-3 text-right">Valor</th>
                                <th class="px-4 py-3">Vencimento</th>
                                <th class="px-4 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-gray-700">
                            @forelse ($multas as $multa)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap">{{ $multa->data_hora_infracao?->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900">{{ $multa->nome_condutor }}</div>
                                        <div class="text-xs text-gray-500">{{ $cpf($multa->cpf) }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap font-mono">{{ $multa->placa }}</td>
                                    <td class="px-4 py-3">
                                        <span class="font-mono text-xs text-gray-500">{{ $multa->codigo_infracao }}</span>
                                        <div>{{ $multa->descricao_infracao }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-right">{{ $multa->pontos }}</td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">{{ $dinheiro($multa->valor) }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        {{ $multa->data_vencimento?->format('d/m/Y') }}
                                        @if ($multa->data_pagamento)
                                            <div class="text-xs text-gray-500">Pago em {{ $multa->data_pagamento->format('d/m/Y') }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium {{ $corStatus($multa->status_multa) }}">
                                            {{ $multa->status_multa }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-10 text-center text-gray-500">
                                        Nenhuma multa encontrada{{ $temFiltro ? ' para os filtros informados.' : '.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                {{ $multas->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
