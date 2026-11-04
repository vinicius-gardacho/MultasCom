@use('App\Support\Formatacao')

<x-layouts.app title="Condutores">
    <x-filter-form :action="route('condutores.index')">
        <div class="sm:col-span-2">
            <label for="q" class="form-label">Pesquisar</label>
            <input id="q" name="q" type="search" value="{{ $filtros['q'] ?? '' }}" maxlength="100" placeholder="Nome, CPF ou número da CNH" class="form-input">
        </div>

        <div>
            <label for="categoria" class="form-label">Categoria da CNH</label>
            <select id="categoria" name="categoria" class="form-input">
                <option value="">Todas</option>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria }}" @selected(($filtros['categoria'] ?? null) === $categoria)>{{ $categoria }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="cnh" class="form-label">Situação da CNH</label>
            <select id="cnh" name="cnh" class="form-input">
                <option value="">Todas</option>
                <option value="valida" @selected(($filtros['cnh'] ?? null) === 'valida')>Dentro da validade</option>
                <option value="vencida" @selected(($filtros['cnh'] ?? null) === 'vencida')>Vencida</option>
            </select>
        </div>
    </x-filter-form>

    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>CNH</th>
                    <th>Validade</th>
                    <th class="text-right">Multas</th>
                    <th class="text-right">Pontos ativos</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($condutores as $condutor)
                    <tr>
                        <td><a href="{{ route('condutores.show', $condutor) }}" class="link">{{ $condutor->nome }}</a></td>
                        <td class="font-mono whitespace-nowrap">{{ Formatacao::cpf($condutor->cpf) }}</td>
                        <td class="whitespace-nowrap"><span class="font-mono">{{ $condutor->numero_cnh }}</span> · {{ $condutor->categoria_cnh }}</td>
                        <td class="whitespace-nowrap">
                            {{ Formatacao::data($condutor->validade_cnh) }}
                            @if ($condutor->cnhVencida())
                                <span class="ml-1 rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800">Vencida</span>
                            @endif
                        </td>
                        <td class="text-right">{{ $condutor->multas_count }}</td>
                        <td class="text-right">{{ (int) $condutor->pontos_ativos }}</td>
                    </tr>
                @empty
                    <x-empty-row colspan="6" message="Nenhum condutor encontrado para os filtros informados." />
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $condutores->links() }}</div>
</x-layouts.app>
