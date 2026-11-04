@use('App\Support\Formatacao')

<x-layouts.app title="Multas">
    <x-filter-form :action="route('multas.index')">
        <div class="sm:col-span-2">
            <label for="q" class="form-label">Pesquisar</label>
            <input id="q" name="q" type="search" value="{{ $filtros['q'] ?? '' }}" maxlength="100" placeholder="Placa, CPF, condutor, código ou descrição" class="form-input">
        </div>

        <div>
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" class="form-input">
                <option value="">Todos</option>
                @foreach ($statusDisponiveis as $status)
                    <option value="{{ $status }}" @selected(($filtros['status'] ?? null) === $status)>{{ $status }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="orgao" class="form-label">Órgão aplicador</label>
            <select id="orgao" name="orgao" class="form-input">
                <option value="">Todos</option>
                @foreach ($orgaos as $orgao)
                    <option value="{{ $orgao->id_orgao }}" @selected((int) ($filtros['orgao'] ?? 0) === $orgao->id_orgao)>{{ $orgao->sigla }} — {{ $orgao->nome }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="data_inicio" class="form-label">Infração a partir de</label>
            <input id="data_inicio" name="data_inicio" type="date" value="{{ $filtros['data_inicio'] ?? '' }}" class="form-input">
        </div>

        <div>
            <label for="data_fim" class="form-label">Infração até</label>
            <input id="data_fim" name="data_fim" type="date" value="{{ $filtros['data_fim'] ?? '' }}" class="form-input">
        </div>
    </x-filter-form>

    <p class="mb-3 text-sm text-slate-600">
        {{ Formatacao::numero($multas->total()) }} multa(s) encontrada(s) · valor total {{ Formatacao::moeda($valorTotal) }}
    </p>

    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Nº</th>
                    <th>Data</th>
                    <th>Infração</th>
                    <th>Placa</th>
                    <th>Condutor</th>
                    <th>Órgão</th>
                    <th class="text-right">Valor</th>
                    <th class="text-right">Pontos</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($multas as $multa)
                    <tr>
                        <td><a href="{{ route('multas.show', $multa->id_multa) }}" class="link">{{ $multa->id_multa }}</a></td>
                        <td class="whitespace-nowrap">{{ Formatacao::dataHora($multa->data_hora_infracao) }}</td>
                        <td><span class="font-mono text-xs text-slate-500">{{ $multa->codigo_infracao }}</span> {{ $multa->descricao_infracao }}</td>
                        <td><a href="{{ route('veiculos.show', $multa->id_veiculo) }}" class="link font-mono">{{ $multa->placa }}</a></td>
                        <td><a href="{{ route('condutores.show', $multa->id_condutor) }}" class="link">{{ $multa->nome_condutor }}</a></td>
                        <td title="{{ $multa->orgao_nome }}">{{ $multa->orgao_sigla }}</td>
                        <td class="text-right whitespace-nowrap">{{ Formatacao::moeda($multa->valor) }}</td>
                        <td class="text-right">{{ $multa->pontos }}</td>
                        <td><x-status-badge :status="$multa->status_multa" /></td>
                    </tr>
                @empty
                    <x-empty-row colspan="9" message="Nenhuma multa encontrada para os filtros informados." />
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $multas->links() }}</div>
</x-layouts.app>
