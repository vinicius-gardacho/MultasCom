<?php

namespace Tests\Feature;

use Tests\Concerns\UsesTestDatabase;
use Tests\TestCase;

class VeiculoTest extends TestCase
{
    use UsesTestDatabase;

    public function test_listagem_exige_login(): void
    {
        $this->get(route('veiculos.index'))->assertRedirect(route('login'));
    }

    public function test_listagem_de_veiculos(): void
    {
        $this->entrarComoAdmin()
            ->get(route('veiculos.index'))
            ->assertOk()
            ->assertSeeInOrder(['TST1A11', 'TST2B22', 'TST3C33'])
            ->assertSeeText('Ana Teste');
    }

    public function test_pesquisa_por_placa_com_hifen(): void
    {
        $this->entrarComoAdmin()
            ->get(route('veiculos.index', ['q' => 'tst-2b22']))
            ->assertOk()
            ->assertSeeText('TST2B22')
            ->assertDontSeeText('TST1A11');
    }

    public function test_filtro_por_tipo(): void
    {
        $this->entrarComoAdmin()
            ->get(route('veiculos.index', ['tipo' => 'caminhão']))
            ->assertSeeText('TST3C33')
            ->assertDontSeeText('TST2B22');
    }

    public function test_tipo_inexistente_e_rejeitado(): void
    {
        $this->entrarComoAdmin()
            ->get(route('veiculos.index', ['tipo' => 'avião']))
            ->assertRedirect(route('veiculos.index'))
            ->assertSessionHasErrors('tipo');
    }

    public function test_detalhe_do_veiculo(): void
    {
        $this->entrarComoAdmin()
            ->get(route('veiculos.show', 2))
            ->assertOk()
            ->assertSeeText('Veículo TST2B22')
            ->assertSeeText('Bruno Teste')
            ->assertSeeText('Infração de teste contestada')
            ->assertSeeText('R$ 488,70');
    }

    public function test_veiculo_inexistente_retorna_404(): void
    {
        $this->entrarComoAdmin()->get(route('veiculos.show', 999))->assertNotFound();
    }
}
