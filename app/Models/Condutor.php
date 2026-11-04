<?php

namespace App\Models;

use App\Models\Concerns\UsesExistingSchema;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Tabela "condutor": dados dos condutores (motoristas).
 */
#[Table(name: 'condutor', key: 'id_condutor', timestamps: false)]
class Condutor extends Model
{
    use UsesExistingSchema;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'validade_cnh' => 'date',
            'data_nascimento' => 'date',
        ];
    }

    /**
     * Veículos vinculados ao condutor (veiculo.condutor_id).
     */
    public function veiculos(): HasMany
    {
        return $this->hasMany(Veiculo::class, 'condutor_id', 'id_condutor');
    }

    /**
     * Multas registradas para o condutor (multa.condutor_id).
     */
    public function multas(): HasMany
    {
        return $this->hasMany(Multa::class, 'condutor_id', 'id_condutor');
    }

    /**
     * Extrato de pontos lançados na CNH (pontuacao_condutor.condutor_id).
     */
    public function pontuacoes(): HasMany
    {
        return $this->hasMany(PontuacaoCondutor::class, 'condutor_id', 'id_condutor');
    }

    /**
     * Pesquisa por nome, CPF ou número da CNH.
     */
    #[Scope]
    protected function pesquisar(Builder $query, ?string $termo): void
    {
        $termo = trim((string) $termo);

        if ($termo === '') {
            return;
        }

        $digitos = preg_replace('/\D/', '', $termo);

        $query->where(function (Builder $query) use ($termo, $digitos) {
            $query->whereLike('nome', '%'.$termo.'%');

            if ($digitos !== '') {
                $query->orWhereLike('cpf', $digitos.'%')
                    ->orWhereLike('numero_cnh', $digitos.'%');
            }
        });
    }

    /**
     * Soma dos pontos ainda não expirados, disponível como "pontos_ativos".
     */
    #[Scope]
    protected function comPontosAtivos(Builder $query): void
    {
        $query->withSum(['pontuacoes as pontos_ativos' => fn (Builder $query) => $query->ativas()], 'pontos');
    }

    public function cnhVencida(): bool
    {
        return $this->validade_cnh->lt(today());
    }
}
