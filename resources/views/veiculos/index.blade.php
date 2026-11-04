<x-layouts.app title="Veículos">
    <x-filter-form :action="route('veiculos.index')">
        <div class="sm:col-span-2">
            <label for="q" class="form-label">Pesquisar</label>
            <input id="q" name="q" type="search" value="{{ $filtros['q'] ?? '' }}" maxlength="100" placeholder="Placa, RENAVAM, marca ou modelo" class="form-input">
        </div>

        <div>
            <label for="tipo" class="form-label">Tipo de veículo</label>
            <select id="tipo" name="tipo" class="form-input">
                <option value="">Todos</option>
                @foreach ($tipos as $tipo)
                    <option value="{{ $tipo }}" @selected(($filtros['tipo'] ?? null) === $tipo)>{{ $tipo }}</option>
                @endforeach
            </select>
        </div>
    </x-filter-form>

    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Placa</th>
                    <th>Veículo</th>
                    <th>Ano</th>
                    <th>Cor</th>
                    <th>Tipo</th>
                    <th>Proprietário</th>
                    <th class="text-right">Multas</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($veiculos as $veiculo)
                    <tr>
                        <td><a href="{{ route('veiculos.show', $veiculo) }}" class="link font-mono">{{ $veiculo->placa }}</a></td>
                        <td>{{ $veiculo->marca }} {{ $veiculo->modelo }}</td>
                        <td class="whitespace-nowrap">{{ $veiculo->ano_fabricacao }}/{{ $veiculo->ano_modelo }}</td>
                        <td>{{ $veiculo->cor ?? '—' }}</td>
                        <td>{{ $veiculo->tipo_veiculo }}</td>
                        <td>
                            @if ($veiculo->proprietario)
                                <a href="{{ route('condutores.show', $veiculo->proprietario) }}" class="link">{{ $veiculo->proprietario->nome }}</a>
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-right">{{ $veiculo->multas_count }}</td>
                    </tr>
                @empty
                    <x-empty-row colspan="7" message="Nenhum veículo encontrado para os filtros informados." />
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $veiculos->links() }}</div>
</x-layouts.app>
