@use('App\Support\Formatacao')

<x-layouts.app :title="'Multa nº '.$multa->id_multa">
    <p class="-mt-4 mb-6"><a href="{{ route('multas.index') }}" class="link text-sm">&larr; Voltar para multas</a></p>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat label="Valor" :value="Formatacao::moeda($multa->valor)" />
        <x-stat label="Pontos" :value="$multa->pontos" />
        <x-stat label="Vencimento" :value="Formatacao::data($multa->data_vencimento)" />
        <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Status</p>
            <p class="mt-2"><x-status-badge :status="$multa->status->descricao" /></p>
            <p class="mt-2 text-xs text-slate-500">Pagamento: {{ Formatacao::data($multa->data_pagamento) }}</p>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <x-card title="Infração">
            <dl class="grid gap-3 text-sm">
                <div><dt class="text-slate-500">Código</dt><dd class="font-mono">{{ $multa->codigo_infracao }}</dd></div>
                <div><dt class="text-slate-500">Descrição</dt><dd>{{ $multa->descricao_infracao }}</dd></div>
                <div><dt class="text-slate-500">Data e hora</dt><dd>{{ Formatacao::dataHora($multa->data_hora_infracao) }}</dd></div>
                <div><dt class="text-slate-500">Local</dt><dd>{{ $multa->local_infracao }}</dd></div>
                <div><dt class="text-slate-500">Órgão aplicador</dt><dd>{{ $multa->orgao->nome }} ({{ $multa->orgao->sigla }}) · {{ $multa->orgao->jurisdicao }}</dd></div>
            </dl>
        </x-card>

        <div class="grid gap-6">
            <x-card title="Condutor">
                <dl class="grid gap-3 text-sm sm:grid-cols-2">
                    <div class="sm:col-span-2"><dt class="text-slate-500">Nome</dt><dd><a href="{{ route('condutores.show', $multa->condutor) }}" class="link">{{ $multa->condutor->nome }}</a></dd></div>
                    <div><dt class="text-slate-500">CPF</dt><dd class="font-mono">{{ Formatacao::cpf($multa->condutor->cpf) }}</dd></div>
                    <div><dt class="text-slate-500">CNH</dt><dd class="font-mono">{{ $multa->condutor->numero_cnh }} · {{ $multa->condutor->categoria_cnh }}</dd></div>
                </dl>
            </x-card>

            <x-card title="Veículo">
                <dl class="grid gap-3 text-sm sm:grid-cols-2">
                    <div><dt class="text-slate-500">Placa</dt><dd><a href="{{ route('veiculos.show', $multa->veiculo) }}" class="link font-mono">{{ $multa->veiculo->placa }}</a></dd></div>
                    <div><dt class="text-slate-500">Marca / modelo</dt><dd>{{ $multa->veiculo->marca }} {{ $multa->veiculo->modelo }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-slate-500">Proprietário</dt><dd>{{ $multa->veiculo->proprietario?->nome ?? 'Não informado' }}</dd></div>
                </dl>
            </x-card>
        </div>
    </div>

    <x-card title="Lançamento de pontos na CNH" class="mt-6">
        @if ($multa->pontuacao)
            <dl class="grid gap-3 text-sm sm:grid-cols-4">
                <div><dt class="text-slate-500">Pontos lançados</dt><dd>{{ $multa->pontuacao->pontos }}</dd></div>
                <div><dt class="text-slate-500">Lançamento</dt><dd>{{ Formatacao::data($multa->pontuacao->data_lancamento) }}</dd></div>
                <div><dt class="text-slate-500">Expiração</dt><dd>{{ Formatacao::data($multa->pontuacao->data_expiracao) }}</dd></div>
                <div><dt class="text-slate-500">Situação</dt><dd>{{ $multa->pontuacao->expirada() ? 'Expirada' : 'Ativa' }}</dd></div>
            </dl>
        @else
            <p class="text-sm text-slate-500">Não há lançamento de pontos registrado para esta multa.</p>
        @endif
    </x-card>
</x-layouts.app>
