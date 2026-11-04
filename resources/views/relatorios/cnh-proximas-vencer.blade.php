@use('App\Support\Formatacao')

<x-layouts.app title="CNHs vencidas ou a vencer">
    <x-relatorios-nav />

    <p class="mb-4 text-sm text-slate-600">Condutores com CNH já vencida ou com validade nos próximos 90 dias.</p>

    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Condutor</th>
                    <th>CNH</th>
                    <th>Categoria</th>
                    <th>Validade</th>
                    <th>Situação</th>
                    <th class="text-right">Multas</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($condutores as $condutor)
                    <tr>
                        <td><a href="{{ route('condutores.show', $condutor->id_condutor) }}" class="link">{{ $condutor->nome_condutor }}</a></td>
                        <td class="font-mono">{{ $condutor->numero_cnh }}</td>
                        <td>{{ $condutor->categoria_cnh }}</td>
                        <td>{{ Formatacao::data($condutor->validade_cnh) }}</td>
                        <td class="whitespace-nowrap">
                            @if ($condutor->dias_para_vencer < 0)
                                <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800">Vencida há {{ abs($condutor->dias_para_vencer) }} dia(s)</span>
                            @else
                                <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800">Vence em {{ $condutor->dias_para_vencer }} dia(s)</span>
                            @endif
                        </td>
                        <td class="text-right">{{ $condutor->qtd_multas_condutor }}</td>
                    </tr>
                @empty
                    <x-empty-row colspan="6" />
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $condutores->links() }}</div>
</x-layouts.app>
