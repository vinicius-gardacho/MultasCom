<?php

namespace App\Http\Controllers;

use App\Http\Requests\CondutorIndexRequest;
use App\Models\Condutor;
use Illuminate\View\View;

class CondutorController extends Controller
{
    public function index(CondutorIndexRequest $request): View
    {
        $filtros = $request->validated();
        $hoje = today()->toDateString();

        $condutores = Condutor::query()
            ->pesquisar($filtros['q'] ?? null)
            ->when($filtros['categoria'] ?? null, fn ($query, $categoria) => $query->where('categoria_cnh', $categoria))
            ->when(($filtros['cnh'] ?? null) === 'vencida', fn ($query) => $query->where('validade_cnh', '<', $hoje))
            ->when(($filtros['cnh'] ?? null) === 'valida', fn ($query) => $query->where('validade_cnh', '>=', $hoje))
            ->withCount('multas')
            ->comPontosAtivos()
            ->orderBy('nome')
            ->orderBy('id_condutor')
            ->paginate(15)
            ->withQueryString();

        return view('condutores.index', [
            'condutores' => $condutores,
            'filtros' => $filtros,
            'categorias' => Condutor::query()->distinct()->orderBy('categoria_cnh')->pluck('categoria_cnh'),
        ]);
    }

    public function show(Condutor $condutor): View
    {
        $condutor->loadCount('multas')
            ->loadSum(['pontuacoes as pontos_ativos' => fn ($query) => $query->ativas()], 'pontos')
            ->load([
                'veiculos' => fn ($query) => $query->orderBy('placa'),
                'multas' => fn ($query) => $query->with(['veiculo', 'orgao', 'status'])->orderByDesc('data_hora_infracao'),
                'pontuacoes' => fn ($query) => $query->with('multa')->orderByDesc('data_lancamento'),
            ]);

        return view('condutores.show', ['condutor' => $condutor]);
    }
}
