<?php

namespace Tests\Feature\Auth;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_visitante_e_redirecionado_para_o_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_tela_de_login_e_exibida(): void
    {
        $this->get(route('login'))->assertOk()->assertSeeText('Acesso ao sistema');
    }

    public function test_login_com_credenciais_validas(): void
    {
        $this->post(route('login'), ['usuario' => 'admin', 'senha' => 'senha-de-teste'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }

    public function test_login_com_senha_invalida(): void
    {
        $this->from(route('login'))
            ->post(route('login'), ['usuario' => 'admin', 'senha' => 'errada'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['usuario' => 'Usuário ou senha inválidos.']);

        $this->assertGuest();
    }

    public function test_login_com_usuario_inexistente(): void
    {
        $this->post(route('login'), ['usuario' => 'outro', 'senha' => 'senha-de-teste'])
            ->assertSessionHasErrors('usuario');

        $this->assertGuest();
    }

    public function test_campos_obrigatorios(): void
    {
        $this->post(route('login'), [])->assertSessionHasErrors(['usuario', 'senha']);
    }

    public function test_login_bloqueado_apos_muitas_tentativas(): void
    {
        foreach (range(1, 5) as $tentativa) {
            $this->post(route('login'), ['usuario' => 'admin', 'senha' => 'errada']);
        }

        $this->post(route('login'), ['usuario' => 'admin', 'senha' => 'senha-de-teste'])
            ->assertSessionHasErrors('usuario');

        $this->assertGuest();
    }

    public function test_login_falha_quando_acesso_nao_esta_configurado(): void
    {
        config(['auth.providers.users.password_hash' => null]);

        $this->post(route('login'), ['usuario' => 'admin', 'senha' => 'senha-de-teste'])
            ->assertSessionHasErrors('usuario');

        $this->assertGuest();
    }

    public function test_logout_encerra_a_sessao(): void
    {
        $this->entrarComoAdmin()
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_somente_login_e_logout_aceitam_metodos_de_escrita(): void
    {
        $rotasDeEscrita = collect(Route::getRoutes()->getRoutes())
            ->filter(fn ($rota) => str_starts_with($rota->getActionName(), 'App\\'))
            ->filter(fn ($rota) => array_diff($rota->methods(), ['GET', 'HEAD']) !== [])
            ->map(fn ($rota) => $rota->uri())
            ->sort()
            ->values()
            ->all();

        $this->assertSame(['login', 'logout'], $rotasDeEscrita);
    }
}
