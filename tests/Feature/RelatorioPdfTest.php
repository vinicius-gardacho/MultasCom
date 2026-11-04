<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\View;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\UsesTestDatabase;
use Tests\TestCase;

class RelatorioPdfTest extends TestCase
{
    use UsesTestDatabase;

    /**
     * @return array<string, array{string, string}>
     */
    public static function relatorios(): array
    {
        return [
            'ranking de infrações' => ['relatorios.ranking-infracoes', 'ranking-infracoes'],
            'veículos mais multados' => ['relatorios.veiculos-mais-multados', 'veiculos-mais-multados'],
            'CNHs a vencer' => ['relatorios.cnh-proximas-vencer', 'cnh-proximas-vencer'],
        ];
    }

    #[DataProvider('relatorios')]
    public function test_exportacao_exige_login(string $rota): void
    {
        $this->get(route($rota.'.pdf'))->assertRedirect(route('login'));
    }

    #[DataProvider('relatorios')]
    public function test_pagina_do_relatorio_tem_botao_de_exportar(string $rota): void
    {
        $this->entrarComoAdmin()
            ->get(route($rota))
            ->assertOk()
            ->assertSee(route($rota.'.pdf'))
            ->assertSeeText('Exportar PDF');
    }

    #[DataProvider('relatorios')]
    public function test_relatorio_e_baixado_em_pdf(string $rota, string $arquivo): void
    {
        $response = $this->entrarComoAdmin()->get(route($rota.'.pdf'));

        $response->assertOk()
            ->assertHeader('Content-Type', 'application/pdf')
            ->assertDownload($arquivo.'-'.today()->format('Y-m-d').'.pdf');

        $this->assertStringStartsWith('%PDF-', $response->getContent());
    }

    public function test_pdf_contem_todos_os_registros_do_relatorio(): void
    {
        $dados = [];
        View::composer('relatorios.pdf', function ($view) use (&$dados) {
            $dados = $view->getData();
        });

        $this->entrarComoAdmin()->get(route('relatorios.ranking-infracoes.pdf'))->assertOk();

        $this->assertSame('Infrações mais cometidas', $dados['titulo']);
        $this->assertCount(4, $dados['linhas']);
        $this->assertSame(['165', 'Infração de teste gravíssima', 1, 7, 'R$ 2.934,70', 'R$ 2.934,70'], $dados['linhas'][0]);
    }
}
