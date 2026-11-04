@use('App\Support\Formatacao')

<x-layouts.app :title="'Veículo '.$veiculo->placa">
    <p class="-mt-4 mb-6"><a href="{{ route('veiculos.index') }}" class="link text-sm">&larr; Voltar para veículos</a></p>

    <div class="grid gap-4 sm:grid-cols-2">
        <x-stat label="Multas registradas" :value="$veiculo->multas->count()" />
        <x-stat label="Valor total das multas" :value="Formatacao::moeda($veiculo->multas->sum('valor'))" />
    </div>

    <x-card title="Dados do veículo" class="mt-6">
        <dl class="grid gap-4 text-sm sm:grid-cols-2 lg:grid-cols-4">
            <div><dt class="text-slate-500">Placa</dt><dd class="font-mono">{{ $veiculo->placa }}</dd></div>
            <div><dt class="text-slate-500">RENAVAM</dt><dd class="font-mono">{{ $veiculo->renavam }}</dd></div>
            <div><dt class="text-slate-500">Marca / modelo</dt><dd>{{ $veiculo->marca }} {{ $veiculo->modelo }}</dd></div>
            <div><dt class="text-slate-500">Ano fabricação / modelo</dt><dd>{{ $veiculo->ano_fabricacao }}/{{ $veiculo->ano_modelo }}</dd></div>
            <div><dt class="text-slate-500">Cor</dt><dd>{{ $veiculo->cor ?? '—' }}</dd></div>
            <div><dt class="text-slate-500">Tipo</dt><dd>{{ $veiculo->tipo_veiculo }}</dd></div>
            <div class="sm:col-span-2">
                <dt class="text-slate-500">Proprietário</dt>
                <dd>
                    @if ($veiculo->proprietario)
                        <a href="{{ route('condutores.show', $veiculo->proprietario) }}" class="link">{{ $veiculo->proprietario->nome }}</a>
                    @else
                        Não informado
                    @endif
                </dd>
            </div>
        </dl>
    </x-card>

    <x-card title="Multas" class="mt-6">
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nº</th>
                        <th>Data</th>
                        <th>Infração</th>
                        <th>Condutor</th>
                        <th>Órgão</th>
                        <th class="text-right">Valor</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($veiculo->multas as $multa)
                        <tr>
                            <td><a href="{{ route('multas.show', $multa) }}" class="link">{{ $multa->id_multa }}</a></td>
                            <td class="whitespace-nowrap">{{ Formatacao::dataHora($multa->data_hora_infracao) }}</td>
                            <td><span class="font-mono text-xs text-slate-500">{{ $multa->codigo_infracao }}</span> {{ $multa->descricao_infracao }}</td>
                            <td><a href="{{ route('condutores.show', $multa->condutor) }}" class="link">{{ $multa->condutor->nome }}</a></td>
                            <td>{{ $multa->orgao->sigla }}</td>
                            <td class="text-right whitespace-nowrap">{{ Formatacao::moeda($multa->valor) }}</td>
                            <td><x-status-badge :status="$multa->status->descricao" /></td>
                        </tr>
                    @empty
                        <x-empty-row colspan="7" message="Nenhuma multa registrada." />
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.app>
