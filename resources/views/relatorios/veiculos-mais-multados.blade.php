@use('App\Support\Formatacao')

<x-layouts.app title="Veículos mais multados">
    <x-relatorios-nav />

    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Placa</th>
                    <th>Veículo</th>
                    <th>Tipo</th>
                    <th>Proprietário</th>
                    <th class="text-right">Multas</th>
                    <th class="text-right">Valor total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($veiculos as $veiculo)
                    <tr>
                        <td><a href="{{ route('veiculos.show', $veiculo->id_veiculo) }}" class="link font-mono">{{ $veiculo->placa }}</a></td>
                        <td>{{ $veiculo->marca }} {{ $veiculo->modelo }}</td>
                        <td>{{ $veiculo->tipo_veiculo }}</td>
                        <td>{{ $veiculo->nome_proprietario ?? '—' }}</td>
                        <td class="text-right">{{ $veiculo->qtd_multas }}</td>
                        <td class="text-right whitespace-nowrap">{{ Formatacao::moeda($veiculo->valor_total_multas) }}</td>
                    </tr>
                @empty
                    <x-empty-row colspan="6" />
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $veiculos->links() }}</div>
</x-layouts.app>
