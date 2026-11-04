@use('App\Support\Formatacao')

<x-layouts.app title="Infrações mais cometidas">
    <x-relatorios-nav />

    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Descrição</th>
                    <th class="text-right">Ocorrências</th>
                    <th class="text-right">Pontos gerados</th>
                    <th class="text-right">Valor médio</th>
                    <th class="text-right">Valor total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($infracoes as $infracao)
                    <tr>
                        <td class="font-mono whitespace-nowrap">
                            <a href="{{ route('multas.index', ['q' => $infracao->codigo_infracao]) }}" class="link">{{ $infracao->codigo_infracao }}</a>
                        </td>
                        <td>{{ $infracao->descricao_infracao }}</td>
                        <td class="text-right">{{ $infracao->quantidade_ocorrencias }}</td>
                        <td class="text-right">{{ $infracao->total_pontos_gerados }}</td>
                        <td class="text-right whitespace-nowrap">{{ Formatacao::moeda($infracao->valor_medio) }}</td>
                        <td class="text-right whitespace-nowrap">{{ Formatacao::moeda($infracao->valor_total) }}</td>
                    </tr>
                @empty
                    <x-empty-row colspan="6" />
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $infracoes->links() }}</div>
</x-layouts.app>
