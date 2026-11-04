<?php

namespace App\Http\Controllers;

use App\Http\Requests\VeiculoIndexRequest;
use App\Models\Veiculo;
use Illuminate\View\View;

class VeiculoController extends Controller
{
    public function index(VeiculoIndexRequest $request): View
    {
        $filtros = $request->validated();

        $veiculos = Veiculo::query()
            ->pesquisar($filtros['q'] ?? null)
            ->when($filtros['tipo'] ?? null, fn ($query, $tipo) => $query->where('tipo_veiculo', $tipo))
            ->with('proprietario')
            ->withCount('multas')
            ->orderBy('placa')
            ->paginate(15)
            ->withQueryString();

        return view('veiculos.index', [
            'veiculos' => $veiculos,
            'filtros' => $filtros,
            'tipos' => Veiculo::query()->distinct()->orderBy('tipo_veiculo')->pluck('tipo_veiculo'),
        ]);
    }

    public function show(Veiculo $veiculo): View
    {
        $veiculo->load([
            'proprietario',
            'multas' => fn ($query) => $query->with(['condutor', 'orgao', 'status'])->orderByDesc('data_hora_infracao'),
        ]);

        return view('veiculos.show', ['veiculo' => $veiculo]);
    }
}
