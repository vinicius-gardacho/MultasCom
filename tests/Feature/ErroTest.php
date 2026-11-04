<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\Concerns\UsesTestDatabase;
use Tests\TestCase;

class ErroTest extends TestCase
{
    use UsesTestDatabase;

    public function test_pagina_inexistente_exibe_404_personalizado(): void
    {
        $this->entrarComoAdmin()
            ->get('/pagina-que-nao-existe')
            ->assertNotFound()
            ->assertSeeText('Registro não encontrado');
    }

    public function test_cabecalhos_de_seguranca(): void
    {
        $this->get(route('login'))
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'same-origin');

        $this->entrarComoAdmin()
            ->get(route('dashboard'))
            ->assertHeader('Cache-Control', 'no-store, private');
    }

    public function test_falha_no_banco_exibe_pagina_amigavel_sem_detalhes_tecnicos(): void
    {
        DB::statement('drop table pontuacao_condutor');

        $this->entrarComoAdmin()
            ->get(route('condutores.index'))
            ->assertStatus(503)
            ->assertSeeText('Banco de dados indisponível')
            ->assertDontSeeText('pontuacao_condutor')
            ->assertDontSeeText('select');
    }

    public function test_falha_no_banco_em_requisicao_json(): void
    {
        DB::statement('drop table pontuacao_condutor');

        $this->entrarComoAdmin()
            ->getJson(route('condutores.index'))
            ->assertStatus(503)
            ->assertExactJson(['message' => 'Banco de dados indisponível.']);
    }
}
