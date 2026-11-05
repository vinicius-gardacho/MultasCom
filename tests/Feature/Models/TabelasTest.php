<?php

namespace Tests\Feature\Models;

use App\Models\Condutor;
use App\Models\Multa;
use App\Models\OrgaoAplicador;
use App\Models\PontuacaoCondutor;
use App\Models\StatusMulta;
use App\Models\Veiculo;
use Tests\Concerns\UsesTestDatabase;
use Tests\TestCase;

class TabelasTest extends TestCase
{
    use UsesTestDatabase;

    public function test_models_usam_nomes_e_chaves_das_tabelas_existentes(): void
    {
        $this->assertSame('condutor', (new Condutor)->getTable());
        $this->assertSame('id_condutor', (new Condutor)->getKeyName());
        $this->assertSame('veiculo', (new Veiculo)->getTable());
        $this->assertSame('id_veiculo', (new Veiculo)->getKeyName());
        $this->assertSame('orgao_aplicador', (new OrgaoAplicador)->getTable());
        $this->assertSame('id_orgao', (new OrgaoAplicador)->getKeyName());
        $this->assertSame('status_multa', (new StatusMulta)->getTable());
        $this->assertSame('id_status', (new StatusMulta)->getKeyName());
        $this->assertSame('multa', (new Multa)->getTable());
        $this->assertSame('id_multa', (new Multa)->getKeyName());
        $this->assertSame('pontuacao_condutor', (new PontuacaoCondutor)->getTable());
        $this->assertSame('id_pontuacao', (new PontuacaoCondutor)->getKeyName());
        $this->assertFalse((new Multa)->usesTimestamps());
    }

    public function test_tabela_e_qualificada_com_o_schema_no_postgresql(): void
    {
        config(['database.connections.pgsql.search_path' => 'viniciusgardacho_trabalho_db']);

        $condutor = (new Condutor)->setConnection('pgsql');

        $this->assertSame('viniciusgardacho_trabalho_db.condutor', $condutor->getTable());
        $this->assertSame('viniciusgardacho_trabalho_db.condutor.id_condutor', $condutor->getQualifiedKeyName());
    }

    public function test_tabela_nao_e_qualificada_quando_o_schema_e_public(): void
    {
        config(['database.connections.pgsql.search_path' => 'public']);

        $this->assertSame('multa', (new Multa)->setConnection('pgsql')->getTable());
    }

    public function test_relacionamentos_da_multa(): void
    {
        $multa = Multa::with(['veiculo.proprietario', 'condutor', 'orgao', 'status', 'pontuacao'])->findOrFail(1);

        $this->assertSame('TST1A11', $multa->veiculo->placa);
        $this->assertSame('Ana Teste', $multa->veiculo->proprietario->nome);
        $this->assertSame('Ana Teste', $multa->condutor->nome);
        $this->assertSame('TESTE-EST', $multa->orgao->sigla);
        $this->assertSame('Pendente', $multa->status->descricao);
        $this->assertSame(7, $multa->pontuacao->pontos);
        $this->assertSame('2934.70', $multa->valor);
    }

    public function test_multa_sem_lancamento_de_pontos(): void
    {
        $this->assertNull(Multa::findOrFail(4)->pontuacao);
    }

    public function test_relacionamentos_do_condutor(): void
    {
        $condutor = Condutor::withCount(['veiculos', 'multas', 'pontuacoes'])->findOrFail(1);

        $this->assertSame(1, $condutor->veiculos_count);
        $this->assertSame(2, $condutor->multas_count);
        $this->assertSame(2, $condutor->pontuacoes_count);
    }

    public function test_pontos_ativos_ignoram_lancamentos_expirados(): void
    {
        $condutores = Condutor::comPontosAtivos()->orderBy('id_condutor')->get();

        $this->assertEquals(7, $condutores[0]->pontos_ativos);
        $this->assertEquals(3, $condutores[1]->pontos_ativos);
        $this->assertNull($condutores[2]->pontos_ativos);
        $this->assertSame(1, PontuacaoCondutor::ativas()->where('condutor_id', 1)->count());
    }

    public function test_situacao_da_cnh(): void
    {
        $this->assertFalse(Condutor::findOrFail(1)->cnhVencida());
        $this->assertTrue(Condutor::findOrFail(3)->cnhVencida());
    }

    public function test_pesquisa_de_condutor_por_nome_cpf_ou_cnh(): void
    {
        $this->assertSame([2], Condutor::pesquisar('bruno')->pluck('id_condutor')->all());
        $this->assertSame([3], Condutor::pesquisar('333.333.333-33')->pluck('id_condutor')->all());
        $this->assertSame([1], Condutor::pesquisar('90000000001')->pluck('id_condutor')->all());
        $this->assertSame(3, Condutor::pesquisar('')->count());
    }

    public function test_pesquisa_de_veiculo_por_placa_ou_modelo(): void
    {
        $this->assertSame([2], Veiculo::pesquisar('tst-2b22')->pluck('id_veiculo')->all());
        $this->assertSame([3], Veiculo::pesquisar('Modelo C')->pluck('id_veiculo')->all());
    }

    public function test_relacionamentos_de_orgao_e_status(): void
    {
        $this->assertSame(2, OrgaoAplicador::findOrFail(1)->multas()->count());
        $this->assertSame(1, StatusMulta::where('descricao', 'Vencida')->firstOrFail()->multas()->count());
        $this->assertSame(2, Veiculo::findOrFail(2)->multas()->count());
    }
}
