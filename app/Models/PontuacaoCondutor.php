<?php

namespace App\Models;

use App\Models\Concerns\UsesExistingSchema;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Tabela "pontuacao_condutor": extrato de pontos lançados na CNH a partir de cada multa.
 */
#[Table(name: 'pontuacao_condutor', key: 'id_pontuacao', timestamps: false)]
class PontuacaoCondutor extends Model
{

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'pontos' => 'integer',
            'data_lancamento' => 'date',
            'data_expiracao' => 'date',
        ];
    }

    public function condutor(): BelongsTo
    {
        return $this->belongsTo(Condutor::class, 'condutor_id', 'id_condutor');
    }

    public function multa(): BelongsTo
    {
        return $this->belongsTo(Multa::class, 'multa_id', 'id_multa');
    }

    /**
     * Lançamentos cuja data de expiração ainda não passou.
     */
    #[Scope]
    protected function ativas(Builder $query): void
    {
        $query->where('data_expiracao', '>=', today()->toDateString());
    }

    public function expirada(): bool
    {
        return $this->data_expiracao->lt(today());
    }
}
