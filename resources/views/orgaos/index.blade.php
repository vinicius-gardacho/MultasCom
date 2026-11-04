@use('App\Support\Formatacao')

<x-layouts.app title="Órgãos aplicadores">
    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Órgão</th>
                    <th>Sigla</th>
                    <th>Jurisdição</th>
                    <th>Contato</th>
                    <th class="text-right">Multas</th>
                    <th class="text-right">Valor total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($orgaos as $orgao)
                    <tr>
                        <td>
                            <p class="font-medium text-slate-900">{{ $orgao->nome }}</p>
                            <p class="text-xs text-slate-500">{{ $orgao->endereco ?? 'Endereço não informado' }}</p>
                        </td>
                        <td class="whitespace-nowrap">{{ $orgao->sigla }}</td>
                        <td>{{ $orgao->jurisdicao }}</td>
                        <td class="text-xs">
                            <p class="whitespace-nowrap">{{ $orgao->telefone ?? '—' }}</p>
                            <p class="break-all">{{ $orgao->email ?? '' }}</p>
                        </td>
                        <td class="text-right">
                            <a href="{{ route('multas.index', ['orgao' => $orgao->id_orgao]) }}" class="link">{{ $orgao->multas_count }}</a>
                        </td>
                        <td class="text-right whitespace-nowrap">{{ Formatacao::moeda($orgao->multas_sum_valor) }}</td>
                    </tr>
                @empty
                    <x-empty-row colspan="6" />
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.app>
