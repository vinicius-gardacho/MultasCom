<?php

namespace App\Support;

use DateTimeInterface;

/**
 * Formatação de valores para exibição no padrão brasileiro.
 */
class Formatacao
{
    public static function moeda(float|int|string|null $valor): string
    {
        return 'R$ '.number_format((float) $valor, 2, ',', '.');
    }

    public static function numero(float|int|string|null $valor): string
    {
        return number_format((float) $valor, 0, ',', '.');
    }

    public static function cpf(?string $cpf): string
    {
        $digitos = preg_replace('/\D/', '', (string) $cpf);

        if (strlen($digitos) !== 11) {
            return (string) $cpf;
        }

        return substr($digitos, 0, 3).'.'.substr($digitos, 3, 3).'.'.substr($digitos, 6, 3).'-'.substr($digitos, 9, 2);
    }

    public static function data(?DateTimeInterface $data): string
    {
        return $data?->format('d/m/Y') ?? '—';
    }

    public static function dataHora(?DateTimeInterface $data): string
    {
        return $data?->format('d/m/Y H:i') ?? '—';
    }
}
