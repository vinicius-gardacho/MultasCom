<?php

namespace App\Models\Views;

use App\Models\Concerns\IsReadOnlyView;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * View "vw_viniciusgardacho_multasDetalhadas": multa com condutor, veículo,
 * órgão aplicador e status já relacionados.
 */
#[Table(name: 'vw_viniciusgardacho_multasdetalhadas', key: 'id_multa', timestamps: false)]
class MultaDetalhada extends Model
{
    use IsReadOnlyView;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data_hora_infracao' => 'datetime',
            'valor' => 'decimal:2',
            'pontos' => 'integer',
            'data_vencimento' => 'date',
            'data_pagamento' => 'date',
        ];
    }

    /**
     * Aplica os filtros da listagem de multas.
     *
     * @param  array{q?: string|null, status?: string|null, orgao?: int|string|null, data_inicio?: string|null, data_fim?: string|null}  $filtros
     */
    #[Scope]
    protected function filtrar(Builder $query, array $filtros): void
    {
        $termo = trim((string) ($filtros['q'] ?? ''));

        if ($termo !== '') {
            $alfanumerico = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $termo));
            $digitos = preg_replace('/\D/', '', $termo);

            $query->where(function (Builder $query) use ($termo, $alfanumerico, $digitos) {
                $query->whereLike('nome_condutor', '%'.$termo.'%')
                    ->orWhereLike('descricao_infracao', '%'.$termo.'%')
                    ->orWhereLike('codigo_infracao', $termo.'%');

                if ($alfanumerico !== '') {
                    $query->orWhereLike('placa', '%'.$alfanumerico.'%');
                }

                if ($digitos !== '') {
                    $query->orWhereLike('cpf', $digitos.'%');
                }
            });
        }

        $query
            ->when($filtros['status'] ?? null, fn (Builder $query, $status) => $query->where('status_multa', $status))
            ->when($filtros['orgao'] ?? null, fn (Builder $query, $orgao) => $query->where('id_orgao', $orgao))
            ->when($filtros['data_inicio'] ?? null, fn (Builder $query, $data) => $query->where('data_hora_infracao', '>=', $data.' 00:00:00'))
            ->when($filtros['data_fim'] ?? null, fn (Builder $query, $data) => $query->where('data_hora_infracao', '<=', $data.' 23:59:59'));
    }
}
