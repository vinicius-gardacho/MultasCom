<?php

namespace Tests\Feature;

use Tests\Concerns\UsesTestDatabase;
use Tests\TestCase;

class LayoutTest extends TestCase
{
    use UsesTestDatabase;

    public function test_tela_de_login_exibe_a_logo(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Multas<span class="text-amber-500">Com</span>', false)
            ->assertSeeText('Consulta de infrações de trânsito')
            ->assertSee(asset('logo.svg'));
    }

    public function test_cabecalho_exibe_a_logo_com_link_para_o_painel(): void
    {
        $this->entrarComoAdmin()
            ->get(route('condutores.index'))
            ->assertOk()
            ->assertSee('aria-label="MultasCom — ir para o painel"', false)
            ->assertSee('Multas<span class="text-amber-500">Com</span>', false);
    }

    public function test_rodape_exibe_o_usuario_conectado(): void
    {
        $this->entrarComoAdmin()
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSeeTextInOrder(['Usuário conectado:', 'admin']);
    }

    public function test_arquivo_da_logo_existe(): void
    {
        $this->assertFileExists(public_path('logo.svg'));
    }
}
