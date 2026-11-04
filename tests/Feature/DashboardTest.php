<?php

namespace Tests\Feature;

use Tests\Concerns\UsesTestDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use UsesTestDatabase;

    public function test_painel_exibe_indicadores_das_tabelas_e_views(): void
    {
        $response = $this->entrarComoAdmin()->get(route('dashboard'));

        $response->assertOk()
            ->assertViewHas('totais', ['condutores' => 3, 'veiculos' => 3, 'multas' => 4, 'orgaos' => 2])
            ->assertViewHas('cnhsVencidas', 1)
            ->assertSeeText('Multas por status')
            ->assertSeeText('R$ 3.129,93')
            ->assertSeeText('TST1A11')
            ->assertSeeText('Ana Teste');
    }
}
