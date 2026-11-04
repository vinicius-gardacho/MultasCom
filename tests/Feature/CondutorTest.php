<?php

namespace Tests\Feature;

use Tests\Concerns\UsesTestDatabase;
use Tests\TestCase;

class CondutorTest extends TestCase
{
    use UsesTestDatabase;

    public function test_listagem_exige_login(): void
    {
        $this->get(route('condutores.index'))->assertRedirect(route('login'));
    }

    public function test_listagem_de_condutores(): void
    {
        $this->entrarComoAdmin()
            ->get(route('condutores.index'))
            ->assertOk()
            ->assertSeeInOrder(['Ana Teste', 'Bruno Teste', 'Carla Teste'])
            ->assertSeeText('111.111.111-11');
    }

    public function test_pesquisa_por_cpf(): void
    {
        $this->entrarComoAdmin()
            ->get(route('condutores.index', ['q' => '222.222']))
            ->assertOk()
            ->assertSeeText('Bruno Teste')
            ->assertDontSeeText('Ana Teste');
    }

    public function test_filtros_de_categoria_e_situacao_da_cnh(): void
    {
        $this->entrarComoAdmin()
            ->get(route('condutores.index', ['categoria' => 'AB']))
            ->assertSeeText('Bruno Teste')
            ->assertDontSeeText('Ana Teste');

        $this->get(route('condutores.index', ['cnh' => 'vencida']))
            ->assertSeeText('Carla Teste')
            ->assertDontSeeText('Bruno Teste');
    }

    public function test_filtro_invalido_volta_para_a_listagem_com_erro(): void
    {
        $this->entrarComoAdmin()
            ->get(route('condutores.index', ['categoria' => 'XYZ', 'q' => str_repeat('a', 101)]))
            ->assertRedirect(route('condutores.index'))
            ->assertSessionHasErrors(['categoria', 'q']);
    }

    public function test_detalhe_do_condutor(): void
    {
        $this->entrarComoAdmin()
            ->get(route('condutores.show', 1))
            ->assertOk()
            ->assertSeeText('Ana Teste')
            ->assertSeeText('TST1A11')
            ->assertSeeText('Infração de teste gravíssima')
            ->assertSeeText('Expirada')
            ->assertSeeText('Ativa')
            ->assertViewHas('condutor', fn ($condutor) => (int) $condutor->pontos_ativos === 7);
    }

    public function test_condutor_inexistente_retorna_404(): void
    {
        $this->entrarComoAdmin()->get(route('condutores.show', 999))->assertNotFound();
        $this->get('/condutores/abc')->assertNotFound();
    }
}
