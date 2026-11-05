<?php

namespace Tests\Feature\Models;

use App\Models\Views\CnhProximaVencer;
use App\Models\Views\MultaDetalhada;
use App\Models\Views\MultaInadimplente;
use App\Models\Views\RankingInfracao;
use App\Models\Views\VeiculoMaisMultado;
use LogicException;
use Tests\Concerns\UsesTestDatabase;
use Tests\TestCase;

class ViewsTest extends TestCase
{
    use UsesTestDatabase;

    public function test_multas_detalhadas_trazem_dados_relacionados(): void
    {
        $multa = MultaDetalhada::findOrFail(3);

        $this->assertSame('Bruno Teste', $multa->nome_condutor);
        $this->assertSame('TST2B22', $multa->placa);
        $this->assertSame('TESTE-EST', $multa->orgao_sigla);
        $this->assertSame('Vencida', $multa->status_multa);
    }

    public function test_filtros_das_multas_detalhadas(): void
    {
        $ids = fn (array $filtros) => MultaDetalhada::filtrar($filtros)->orderBy('id_multa')->pluck('id_multa')->all();

        $this->assertSame([1, 2, 3, 4], $ids([]));
        $this->assertSame([3, 4], $ids(['q' => 'tst2b22']));
        $this->assertSame([1, 2], $ids(['q' => '111.111']));
        $this->assertSame([2], $ids(['q' => '218']));
        $this->assertSame([4], $ids(['status' => 'Contestada']));
        $this->assertSame([2, 4], $ids(['orgao' => 2]));
        $this->assertSame([1, 4], $ids(['data_inicio' => today()->subDays(40)->toDateString()]));
        $this->assertSame([2, 3], $ids(['data_fim' => today()->subDays(60)->toDateString()]));
    }

    public function test_multas_inadimplentes(): void
    {
        $this->assertSame([3, 1], MultaInadimplente::pluck('id_multa')->all());
        $this->assertSame(30, MultaInadimplente::findOrFail(3)->dias_em_atraso);
    }

    public function test_ranking_de_infracoes(): void
    {
        $primeira = RankingInfracao::first();

        $this->assertSame(4, RankingInfracao::count());
        $this->assertSame('165', $primeira->codigo_infracao);
        $this->assertSame(1, $primeira->quantidade_ocorrencias);
    }

    public function test_veiculos_mais_multados(): void
    {
        $veiculos = VeiculoMaisMultado::get();

        $this->assertCount(2, $veiculos);
        $this->assertSame('TST1A11', $veiculos[0]->placa);
        $this->assertSame(2, $veiculos[0]->qtd_multas);
    }

    public function test_cnh_proximas_de_vencer(): void
    {
        $this->assertSame([3, 1], CnhProximaVencer::pluck('id_condutor')->all());
        $this->assertSame(-10, CnhProximaVencer::findOrFail(3)->dias_para_vencer);
    }

    public function test_views_sao_somente_leitura(): void
    {
        $this->expectException(LogicException::class);

        RankingInfracao::first()->save();
    }
}
