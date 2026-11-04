<?php

namespace App\Models;

use App\Models\Concerns\UsesExistingSchema;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Tabela "veiculo": veículos vinculados a um condutor/proprietário.
 */
#[Table(name: 'veiculo', key: 'id_veiculo', timestamps: false)]
class Veiculo extends Model
{

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'ano_fabricacao' => 'integer',
            'ano_modelo' => 'integer',
        ];
    }

    /**
     * Condutor proprietário (veiculo.condutor_id, coluna que aceita nulo).
     */
    public function proprietario(): BelongsTo
    {
        return $this->belongsTo(Condutor::class, 'condutor_id', 'id_condutor');
    }

    /**
     * Multas registradas para o veículo (multa.veiculo_id).
     */
    public function multas(): HasMany
    {
        return $this->hasMany(Multa::class, 'veiculo_id', 'id_veiculo');
    }

    /**
     * Pesquisa por placa, RENAVAM, marca ou modelo.
     */
    #[Scope]
    protected function pesquisar(Builder $query, ?string $termo): void
    {
        $termo = trim((string) $termo);

        if ($termo === '') {
            return;
        }

        $placa = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $termo));

        $query->where(function (Builder $query) use ($termo, $placa) {
            $query->whereLike('marca', '%'.$termo.'%')
                ->orWhereLike('modelo', '%'.$termo.'%');

            if ($placa !== '') {
                $query->orWhereLike('placa', '%'.$placa.'%')
                    ->orWhereLike('renavam', $placa.'%');
            }
        });
    }
}
