@use('App\Support\Formatacao')

<x-layouts.app title="Painel">
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <x-stat label="Condutores" :value="Formatacao::numero($totais['condutores'])" />
        <x-stat label="Veículos" :value="Formatacao::numero($totais['veiculos'])" />
        <x-stat label="Multas" :value="Formatacao::numero($totais['multas'])" />
        <x-stat label="Órgãos aplicadores" :value="Formatacao::numero($totais['orgaos'])" />
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <x-card title="Multas por status" :action="route('multas.index')" class="lg:col-span-2">
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th class="text-right">Quantidade</th>
                            <th class="text-right">Valor total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($statusMultas as $status)
                            <tr>
                                <td><a href="{{ route('multas.index', ['status' => $status->descricao]) }}"><x-status-badge :status="$status->descricao" /></a></td>
                                <td class="text-right">{{ Formatacao::numero($status->multas_count) }}</td>
                                <td class="text-right whitespace-nowrap">{{ Formatacao::moeda($status->multas_sum_valor) }}</td>
                            </tr>
                        @empty
                            <x-empty-row colspan="3" />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <div class="grid gap-4">
            <a href="{{ route('multas.inadimplentes') }}" class="block rounded-lg hover:ring-2 hover:ring-red-200">
                <x-stat label="Multas inadimplentes"
                        :value="Formatacao::numero($inadimplencia['quantidade'])"
                        :hint="'Total em atraso: '.Formatacao::moeda($inadimplencia['valor'])"
                        class="h-full border-red-200" />
            </a>
            <a href="{{ route('relatorios.cnh-proximas-vencer') }}" class="block rounded-lg hover:ring-2 hover:ring-slate-200">
                <x-stat label="CNHs vencidas"
                        :value="Formatacao::numero($cnhsVencidas)"
                        hint="Condutores com validade da CNH já expirada."
                        class="h-full" />
            </a>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <x-card title="Infrações mais cometidas" :action="route('relatorios.ranking-infracoes')">
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Descrição</th>
                            <th class="text-right">Ocorrências</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($rankingInfracoes as $infracao)
                            <tr>
                                <td class="whitespace-nowrap">{{ $infracao->codigo_infracao }}</td>
                                <td>{{ $infracao->descricao_infracao }}</td>
                                <td class="text-right">{{ $infracao->quantidade_ocorrencias }}</td>
                            </tr>
                        @empty
                            <x-empty-row colspan="3" />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <x-card title="Veículos mais multados" :action="route('relatorios.veiculos-mais-multados')">
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Placa</th>
                            <th>Veículo</th>
                            <th class="text-right">Multas</th>
                            <th class="text-right">Valor total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($veiculosMaisMultados as $veiculo)
                            <tr>
                                <td class="font-mono whitespace-nowrap">{{ $veiculo->placa }}</td>
                                <td>{{ $veiculo->marca }} {{ $veiculo->modelo }}</td>
                                <td class="text-right">{{ $veiculo->qtd_multas }}</td>
                                <td class="text-right whitespace-nowrap">{{ Formatacao::moeda($veiculo->valor_total_multas) }}</td>
                            </tr>
                        @empty
                            <x-empty-row colspan="4" />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <x-card title="CNHs que vencem nos próximos 90 dias" :action="route('relatorios.cnh-proximas-vencer')" class="lg:col-span-2">
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Condutor</th>
                            <th>CNH</th>
                            <th>Categoria</th>
                            <th>Validade</th>
                            <th class="text-right">Dias restantes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($cnhsAVencer as $cnh)
                            <tr>
                                <td>{{ $cnh->nome_condutor }}</td>
                                <td class="font-mono">{{ $cnh->numero_cnh }}</td>
                                <td>{{ $cnh->categoria_cnh }}</td>
                                <td>{{ Formatacao::data($cnh->validade_cnh) }}</td>
                                <td class="text-right">{{ $cnh->dias_para_vencer }}</td>
                            </tr>
                        @empty
                            <x-empty-row colspan="5" message="Nenhuma CNH vence nos próximos 90 dias." />
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</x-layouts.app>
