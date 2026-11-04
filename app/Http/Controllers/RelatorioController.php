<?php

namespace App\Http\Controllers;

use App\Models\Views\CnhProximaVencer;
use App\Models\Views\RankingInfracao;
use App\Models\Views\VeiculoMaisMultado;
use App\Support\Formatacao;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Illuminate\View\View;

/**
 * Relatórios baseados nas views já existentes no banco, com exportação em PDF.
 */
class RelatorioController extends Controller
{
    public function rankingInfracoes(): View
    {
        return view('relatorios.ranking-infracoes', [
            'infracoes' => $this->consultaRankingInfracoes()->paginate(20),
        ]);
    }

    public function rankingInfracoesPdf(): Response
    {
        $linhas = $this->consultaRankingInfracoes()->get()->map(fn (RankingInfracao $infracao) => [
            $infracao->codigo_infracao,
            $infracao->descricao_infracao,
            $infracao->quantidade_ocorrencias,
            $infracao->total_pontos_gerados,
            Formatacao::moeda($infracao->valor_medio),
            Formatacao::moeda($infracao->valor_total),
        ]);

        return $this->pdf('ranking-infracoes', 'Infrações mais cometidas', null, [
            'Código' => 'left',
            'Descrição' => 'left',
            'Ocorrências' => 'right',
            'Pontos gerados' => 'right',
            'Valor médio' => 'right',
            'Valor total' => 'right',
        ], $linhas->all());
    }

    public function veiculosMaisMultados(): View
    {
        return view('relatorios.veiculos-mais-multados', [
            'veiculos' => $this->consultaVeiculosMaisMultados()->paginate(20),
        ]);
    }

    public function veiculosMaisMultadosPdf(): Response
    {
        $linhas = $this->consultaVeiculosMaisMultados()->get()->map(fn (VeiculoMaisMultado $veiculo) => [
            $veiculo->placa,
            $veiculo->marca.' '.$veiculo->modelo,
            $veiculo->tipo_veiculo,
            $veiculo->nome_proprietario ?? '—',
            $veiculo->qtd_multas,
            Formatacao::moeda($veiculo->valor_total_multas),
        ]);

        return $this->pdf('veiculos-mais-multados', 'Veículos mais multados', null, [
            'Placa' => 'left',
            'Veículo' => 'left',
            'Tipo' => 'left',
            'Proprietário' => 'left',
            'Multas' => 'right',
            'Valor total' => 'right',
        ], $linhas->all());
    }

    public function cnhProximasVencer(): View
    {
        return view('relatorios.cnh-proximas-vencer', [
            'condutores' => $this->consultaCnhProximasVencer()->paginate(20),
        ]);
    }

    public function cnhProximasVencerPdf(): Response
    {
        $linhas = $this->consultaCnhProximasVencer()->get()->map(fn (CnhProximaVencer $condutor) => [
            $condutor->nome_condutor,
            $condutor->numero_cnh,
            $condutor->categoria_cnh,
            Formatacao::data($condutor->validade_cnh),
            $condutor->dias_para_vencer < 0
                ? 'Vencida há '.abs($condutor->dias_para_vencer).' dia(s)'
                : 'Vence em '.$condutor->dias_para_vencer.' dia(s)',
            $condutor->qtd_multas_condutor,
        ]);

        return $this->pdf('cnh-proximas-vencer', 'CNHs vencidas ou a vencer', 'Condutores com CNH já vencida ou com validade nos próximos 90 dias.', [
            'Condutor' => 'left',
            'CNH' => 'left',
            'Categoria' => 'left',
            'Validade' => 'left',
            'Situação' => 'left',
            'Multas' => 'right',
        ], $linhas->all());
    }

    private function consultaRankingInfracoes(): Builder
    {
        return RankingInfracao::query()
            ->orderByDesc('quantidade_ocorrencias')
            ->orderByDesc('valor_total')
            ->orderBy('codigo_infracao');
    }

    private function consultaVeiculosMaisMultados(): Builder
    {
        return VeiculoMaisMultado::query()
            ->orderByDesc('qtd_multas')
            ->orderByDesc('valor_total_multas')
            ->orderBy('placa');
    }

    private function consultaCnhProximasVencer(): Builder
    {
        return CnhProximaVencer::query()
            ->orderBy('validade_cnh')
            ->orderBy('id_condutor');
    }

    /**
     * Gera o PDF de um relatório com todos os registros (sem paginação).
     *
     * @param  array<string, string>  $colunas  título da coluna => alinhamento (left|right)
     * @param  array<int, array<int, string|int|null>>  $linhas
     */
    private function pdf(string $arquivo, string $titulo, ?string $descricao, array $colunas, array $linhas): Response
    {
        $geradoEm = now();

        $pdf = Pdf::loadView('relatorios.pdf', [
            'titulo' => $titulo,
            'descricao' => $descricao,
            'colunas' => $colunas,
            'linhas' => $linhas,
            'geradoEm' => $geradoEm,
        ])
            ->setPaper('a4', 'landscape')
            // Embute só os caracteres usados da fonte: o arquivo cai de ~900 KB para ~50 KB.
            ->setOption('isFontSubsettingEnabled', true);

        // O total de páginas só é conhecido após renderizar, por isso a numeração é
        // desenhada aqui (o counter(pages) do CSS não é suportado pelo dompdf).
        $pdf->render();
        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->getCanvas();
        $fonte = $dompdf->getFontMetrics()->getFont('DejaVu Sans');
        $largura = $dompdf->getFontMetrics()->getTextWidth('Página 00 de 00', $fonte, 6);
        $canvas->page_text(
            $canvas->get_width() - 24 - $largura,
            $canvas->get_height() - 27,
            'Página {PAGE_NUM} de {PAGE_COUNT}',
            $fonte,
            6,
            [0.39, 0.45, 0.55],
        );

        return $pdf->download($arquivo.'-'.$geradoEm->format('Y-m-d').'.pdf');
    }
}
