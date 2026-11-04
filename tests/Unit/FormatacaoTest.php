<?php

namespace Tests\Unit;

use App\Support\Formatacao;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class FormatacaoTest extends TestCase
{
    public function test_formata_moeda_em_reais(): void
    {
        $this->assertSame('R$ 2.934,70', Formatacao::moeda('2934.70'));
        $this->assertSame('R$ 0,00', Formatacao::moeda(null));
    }

    public function test_formata_numero_inteiro(): void
    {
        $this->assertSame('1.215', Formatacao::numero(1215));
    }

    public function test_formata_cpf(): void
    {
        $this->assertSame('100.000.000-37', Formatacao::cpf('10000000037'));
        $this->assertSame('123', Formatacao::cpf('123'));
    }

    public function test_formata_datas(): void
    {
        $data = CarbonImmutable::create(2026, 2, 7, 5, 34);

        $this->assertSame('07/02/2026', Formatacao::data($data));
        $this->assertSame('07/02/2026 05:34', Formatacao::dataHora($data));
        $this->assertSame('—', Formatacao::data(null));
    }
}
