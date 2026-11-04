<?php

namespace Tests\Feature;

use Tests\Concerns\UsesTestDatabase;
use Tests\TestCase;

class MultaTest extends TestCase
{
    use UsesTestDatabase;

    public function test_paginas_de_multas_exigem_login(): void
    {
        $this->get(route('multas.index'))->assertRedirect(route('login'));
        $this->get(route('multas.inadimplentes'))->assertRedirect(route('login'));
        $this->get(route('multas.show', 1))->assertRedirect(route('login'));
    }

    public function test_listagem_ordenada_da_mais_recente_para_a_mais_antiga(): void
    {
        $this->entrarComoAdmin()
            ->get(route('multas.index'))
            ->assertOk()
            ->assertSeeInOrder(['Infração de teste contestada', 'Infração de teste gravíssima', 'Infração de teste leve', 'Infração de teste média'])
            ->assertSeeText('4 multa(s) encontrada(s) · valor total R$ 3.553,56');
    }

    public function test_filtros_combinados(): void
    {
        $this->entrarComoAdmin()
            ->get(route('multas.index', ['status' => 'Paga', 'orgao' => 2]))
            ->assertOk()
            ->assertSeeText('Infração de teste média')
            ->assertDontSeeText('Infração de teste contestada')
            ->assertSeeText('1 multa(s) encontrada(s) · valor total R$ 130,16');
    }

    public function test_filtro_por_periodo(): void
    {
        $this->entrarComoAdmin()
            ->get(route('multas.index', [
                'data_inicio' => today()->subDays(61)->toDateString(),
                'data_fim' => today()->subDays(39)->toDateString(),
            ]))
            ->assertSeeText('Infração de teste gravíssima')
            ->assertSeeText('Infração de teste leve')
            ->assertDontSeeText('Infração de teste média');
    }

    public function test_filtros_invalidos_sao_rejeitados(): void
    {
        $this->entrarComoAdmin()
            ->get(route('multas.index', [
                'status' => 'Inexistente',
                'orgao' => 'abc',
                'data_inicio' => '2026-02-10',
                'data_fim' => '2026-02-01',
            ]))
            ->assertRedirect(route('multas.index'))
            ->assertSessionHasErrors(['status', 'orgao', 'data_fim']);

        $this->get(route('multas.index', ['data_inicio' => '31/12/2026']))
            ->assertSessionHasErrors('data_inicio');
    }

    public function test_multas_inadimplentes(): void
    {
        $this->entrarComoAdmin()
            ->get(route('multas.inadimplentes'))
            ->assertOk()
            ->assertSeeInOrder(['Infração de teste leve', 'Infração de teste gravíssima'])
            ->assertDontSeeText('Infração de teste contestada')
            ->assertSeeText('R$ 3.129,93');
    }

    public function test_detalhe_da_multa(): void
    {
        $this->entrarComoAdmin()
            ->get(route('multas.show', 1))
            ->assertOk()
            ->assertSeeText('Multa nº 1')
            ->assertSeeText('Infração de teste gravíssima')
            ->assertSeeText('Órgão de Teste Estadual')
            ->assertSeeText('TST1A11')
            ->assertSeeText('Pontos lançados');
    }

    public function test_detalhe_de_multa_sem_lancamento_de_pontos(): void
    {
        $this->entrarComoAdmin()
            ->get(route('multas.show', 4))
            ->assertOk()
            ->assertSeeText('Não há lançamento de pontos registrado para esta multa.');
    }

    public function test_multa_inexistente_retorna_404(): void
    {
        $this->entrarComoAdmin()->get(route('multas.show', 999))->assertNotFound();
    }
}
