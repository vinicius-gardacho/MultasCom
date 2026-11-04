<?php

namespace Tests\Feature;

use Tests\Concerns\UsesTestDatabase;
use Tests\TestCase;

class RelatorioTest extends TestCase
{
    use UsesTestDatabase;

    public function test_paginas_exigem_login(): void
    {
        foreach (['orgaos.index', 'relatorios.ranking-infracoes', 'relatorios.veiculos-mais-multados', 'relatorios.cnh-proximas-vencer'] as $rota) {
            $this->get(route($rota))->assertRedirect(route('login'));
        }
    }

    public function test_orgaos_aplicadores_com_totais(): void
    {
        $this->entrarComoAdmin()
            ->get(route('orgaos.index'))
            ->assertOk()
            ->assertSeeInOrder(['Órgão de Teste Estadual', 'Órgão de Teste Federal'])
            ->assertSeeText('R$ 3.129,93')
            ->assertSeeText('R$ 423,63');
    }

    public function test_ranking_de_infracoes(): void
    {
        $this->entrarComoAdmin()
            ->get(route('relatorios.ranking-infracoes'))
            ->assertOk()
            ->assertSeeInOrder(['Infração de teste gravíssima', 'Infração de teste contestada', 'Infração de teste leve', 'Infração de teste média']);
    }

    public function test_veiculos_mais_multados(): void
    {
        $this->entrarComoAdmin()
            ->get(route('relatorios.veiculos-mais-multados'))
            ->assertOk()
            ->assertSeeInOrder(['TST1A11', 'TST2B22'])
            ->assertDontSeeText('TST3C33');
    }

    public function test_cnh_proximas_de_vencer(): void
    {
        $this->entrarComoAdmin()
            ->get(route('relatorios.cnh-proximas-vencer'))
            ->assertOk()
            ->assertSeeInOrder(['Carla Teste', 'Ana Teste'])
            ->assertSeeText('Vencida há 10 dia(s)')
            ->assertSeeText('Vence em 30 dia(s)')
            ->assertDontSeeText('Bruno Teste');
    }
}
