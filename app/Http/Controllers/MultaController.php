<?php

namespace App\Http\Controllers;

use App\Http\Requests\MultaIndexRequest;
use App\Models\Multa;
use App\Models\OrgaoAplicador;
use App\Models\StatusMulta;
use App\Models\Views\MultaDetalhada;
use App\Models\Views\MultaInadimplente;
use Illuminate\View\View;

class MultaController extends Controller
{
    /**
     * Listagem baseada na view vw_viniciusgardacho_multasDetalhadas, que já traz
     * condutor, veículo, órgão e status em uma única consulta.
     */
    public function index(MultaIndexRequest $request): View
    {
        $filtros = $request->validated();
        $consulta = MultaDetalhada::query()->filtrar($filtros);

        return view('multas.index', [
            'multas' => (clone $consulta)
                ->orderByDesc('data_hora_infracao')
                ->orderByDesc('id_multa')
                ->paginate(20)
                ->withQueryString(),
            'valorTotal' => (clone $consulta)->sum('valor'),
            'filtros' => $filtros,
            'statusDisponiveis' => StatusMulta::orderBy('id_status')->pluck('descricao'),
            'orgaos' => OrgaoAplicador::orderBy('sigla')->get(['id_orgao', 'sigla', 'nome']),
        ]);
    }

    /**
     * Multas inadimplentes, conforme a regra da view vw_viniciusgardacho_multasInadimplentes.
     */
    public function inadimplentes(): View
    {
        return view('multas.inadimplentes', [
            'multas' => MultaInadimplente::query()
                ->orderByDesc('dias_em_atraso')
                ->orderBy('id_multa')
                ->paginate(20),
            'quantidade' => MultaInadimplente::count(),
            'valorTotal' => MultaInadimplente::sum('valor'),
        ]);
    }

    public function show(Multa $multa): View
    {
        $multa->load(['veiculo.proprietario', 'condutor', 'orgao', 'status', 'pontuacao']);

        return view('multas.show', ['multa' => $multa]);
    }
}
