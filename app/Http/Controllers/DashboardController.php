<?php

namespace App\Http\Controllers;

use App\Models\Condutor;
use App\Models\Multa;
use App\Models\OrgaoAplicador;
use App\Models\StatusMulta;
use App\Models\Veiculo;
use App\Models\Views\CnhProximaVencer;
use App\Models\Views\MultaInadimplente;
use App\Models\Views\RankingInfracao;
use App\Models\Views\VeiculoMaisMultado;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'totais' => [
                'condutores' => Condutor::count(),
                'veiculos' => Veiculo::count(),
                'multas' => Multa::count(),
                'orgaos' => OrgaoAplicador::count(),
            ],
            'inadimplencia' => [
                'quantidade' => MultaInadimplente::count(),
                'valor' => MultaInadimplente::sum('valor'),
            ],
            'statusMultas' => StatusMulta::withCount('multas')
                ->withSum('multas', 'valor')
                ->orderBy('id_status')
                ->get(),
            'cnhsVencidas' => CnhProximaVencer::where('dias_para_vencer', '<', 0)->count(),
            'cnhsAVencer' => CnhProximaVencer::where('dias_para_vencer', '>=', 0)
                ->orderBy('validade_cnh')
                ->limit(5)
                ->get(),
            'rankingInfracoes' => RankingInfracao::orderByDesc('quantidade_ocorrencias')
                ->orderByDesc('valor_total')
                ->limit(5)
                ->get(),
            'veiculosMaisMultados' => VeiculoMaisMultado::orderByDesc('qtd_multas')
                ->orderByDesc('valor_total_multas')
                ->limit(5)
                ->get(),
        ]);
    }
}
