@use('App\Support\Formatacao')

<x-layouts.app :title="$condutor->nome">
    <p class="-mt-4 mb-6"><a href="{{ route('condutores.index') }}" class="link text-sm">&larr; Voltar para condutores</a></p>

    <div class="grid gap-4 sm:grid-cols-3">
        <x-stat label="Multas registradas" :value="$condutor->multas_count" />
        <x-stat label="Pontos ativos" :value="(int) $condutor->pontos_ativos" hint="Soma dos lançamentos com data de expiração não vencida." />
        <x-stat label="Validade da CNH" :value="Formatacao::data($condutor->validade_cnh)" :hint="$condutor->cnhVencida() ? 'CNH vencida' : 'CNH dentro da validade'" @class(['border-red-200' => $condutor->cnhVencida()]) />
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <x-card title="Dados do condutor">
            <dl class="grid gap-3 text-sm">
                <div><dt class="text-slate-500">CPF</dt><dd class="font-mono">{{ Formatacao::cpf($condutor->cpf) }}</dd></div>
                <div><dt class="text-slate-500">Número da CNH</dt><dd class="font-mono">{{ $condutor->numero_cnh }}</dd></div>
                <div><dt class="text-slate-500">Categoria</dt><dd>{{ $condutor->categoria_cnh }}</dd></div>
                <div><dt class="text-slate-500">Data de nascimento</dt><dd>{{ Formatacao::data($condutor->data_nascimento) }}</dd></div>
                <div><dt class="text-slate-500">Endereço</dt><dd>{{ $condutor->endereco ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Telefone</dt><dd>{{ $condutor->telefone ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">E-mail</dt><dd class="break-all">{{ $condutor->email ?? '—' }}</dd></div>
            </dl>
        </x-card>

        <x-card title="Veículos" class="lg:col-span-2">
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Placa</th>
                            <th>Veículo</th>
                            <th>Tipo</th>
                            <th>Ano</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($condutor->veiculos as $veiculo)
                            <tr>
                                <td><a href="{{ route('veiculos.show', $veiculo) }}" class="link font-mono">{{ $veiculo->placa }}</a></td>
                                <td>{{ $veiculo->marca }} {{ $veiculo->modelo }}</td>
                                <td>{{ $veiculo->tipo_veiculo }}</td>
                                <td>{{ $veiculo->ano_fabricacao }}/{{ $veiculo->ano_modelo }}</td>
                            </tr>
                        @empty
                            <x-empty-row colspan="4" message="Nenhum veículo vinculado." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>

    <x-card title="Multas" class="mt-6">
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nº</th>
                        <th>Data</th>
                        <th>Infração</th>
                        <th>Placa</th>
                        <th>Órgão</th>
                        <th class="text-right">Valor</th>
                        <th class="text-right">Pontos</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($condutor->multas as $multa)
                        <tr>
                            <td><a href="{{ route('multas.show', $multa) }}" class="link">{{ $multa->id_multa }}</a></td>
                            <td class="whitespace-nowrap">{{ Formatacao::dataHora($multa->data_hora_infracao) }}</td>
                            <td><span class="font-mono text-xs text-slate-500">{{ $multa->codigo_infracao }}</span> {{ $multa->descricao_infracao }}</td>
                            <td><a href="{{ route('veiculos.show', $multa->veiculo) }}" class="link font-mono">{{ $multa->veiculo->placa }}</a></td>
                            <td>{{ $multa->orgao->sigla }}</td>
                            <td class="text-right whitespace-nowrap">{{ Formatacao::moeda($multa->valor) }}</td>
                            <td class="text-right">{{ $multa->pontos }}</td>
                            <td><x-status-badge :status="$multa->status->descricao" /></td>
                        </tr>
                    @empty
                        <x-empty-row colspan="8" message="Nenhuma multa registrada." />
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>

    <x-card title="Extrato de pontuação" class="mt-6">
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Multa</th>
                        <th>Infração</th>
                        <th>Lançamento</th>
                        <th>Expiração</th>
                        <th class="text-right">Pontos</th>
                        <th>Situação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($condutor->pontuacoes as $pontuacao)
                        <tr>
                            <td><a href="{{ route('multas.show', $pontuacao->multa_id) }}" class="link">{{ $pontuacao->multa_id }}</a></td>
                            <td>{{ $pontuacao->multa?->descricao_infracao ?? '—' }}</td>
                            <td>{{ Formatacao::data($pontuacao->data_lancamento) }}</td>
                            <td>{{ Formatacao::data($pontuacao->data_expiracao) }}</td>
                            <td class="text-right">{{ $pontuacao->pontos }}</td>
                            <td>
                                @if ($pontuacao->expirada())
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">Expirada</span>
                                @else
                                    <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800">Ativa</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <x-empty-row colspan="6" message="Nenhum lançamento de pontos." />
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.app>
