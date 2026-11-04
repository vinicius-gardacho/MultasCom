<?php

namespace App\Models;

use App\Models\Concerns\UsesExistingSchema;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Tabela "multa": registro central das infrações cometidas.
 */
#[Table(name: 'multa', key: 'id_multa', timestamps: false)]
class Multa extends Model
{

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

    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(Veiculo::class, 'veiculo_id', 'id_veiculo');
    }

    public function condutor(): BelongsTo
    {
        return $this->belongsTo(Condutor::class, 'condutor_id', 'id_condutor');
    }

    public function orgao(): BelongsTo
    {
        return $this->belongsTo(OrgaoAplicador::class, 'orgao_id', 'id_orgao');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(StatusMulta::class, 'status_id', 'id_status');
    }

    /**
     * Lançamento de pontos gerado pela multa (pontuacao_condutor.multa_id é único).
     * Nem toda multa possui lançamento na base atual.
     */
    public function pontuacao(): HasOne
    {
        return $this->hasOne(PontuacaoCondutor::class, 'multa_id', 'id_multa');
    }
}
