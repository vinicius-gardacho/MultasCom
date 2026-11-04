@use('App\Support\Formatacao')

<x-layouts.app title="Multas inadimplentes">
    <p class="-mt-4 mb-6 text-sm text-slate-600">
        Multas sem pagamento, com status Pendente ou Vencida e data de vencimento anterior a hoje.
    </p>

    <div class="mb-6 grid gap-4 sm:grid-cols-2">
        <x-stat label="Multas em atraso" :value="Formatacao::numero($quantidade)" />
        <x-stat label="Valor total em atraso" :value="Formatacao::moeda($valorTotal)" />
    </div>

    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Nº</th>
                    <th>Condutor</th>
                    <th>CPF</th>
                    <th>Telefone</th>
                    <th>Placa</th>
                    <th>Infração</th>
                    <th class="text-right">Valor</th>
                    <th>Vencimento</th>
                    <th class="text-right">Dias em atraso</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($multas as $multa)
                    <tr>
                        <td><a href="{{ route('multas.show', $multa->id_multa) }}" class="link">{{ $multa->id_multa }}</a></td>
                        <td>{{ $multa->nome_condutor }}</td>
                        <td class="font-mono whitespace-nowrap">{{ Formatacao::cpf($multa->cpf) }}</td>
                        <td class="whitespace-nowrap">{{ $multa->telefone ?? '—' }}</td>
                        <td class="font-mono">{{ $multa->placa }}</td>
                        <td>{{ $multa->descricao_infracao }}</td>
                        <td class="text-right whitespace-nowrap">{{ Formatacao::moeda($multa->valor) }}</td>
                        <td>{{ Formatacao::data($multa->data_vencimento) }}</td>
                        <td class="text-right">{{ $multa->dias_em_atraso }}</td>
                        <td><x-status-badge :status="$multa->status_multa" /></td>
                    </tr>
                @empty
                    <x-empty-row colspan="10" message="Nenhuma multa inadimplente." />
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $multas->links() }}</div>
</x-layouts.app>
