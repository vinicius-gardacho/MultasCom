<?php

namespace App\Models\Views;

use App\Models\Concerns\IsReadOnlyView;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * View "vw_viniciusgardacho_rankingInfracoes": infrações agrupadas por código e
 * descrição, ordenadas pela quantidade de ocorrências. Não possui chave primária.
 */
#[Table(name: 'vw_viniciusgardacho_rankinginfracoes', key: false, incrementing: false, timestamps: false)]
class RankingInfracao extends Model
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
            'quantidade_ocorrencias' => 'integer',
            'total_pontos_gerados' => 'integer',
            'valor_medio' => 'decimal:2',
            'valor_total' => 'decimal:2',
        ];
    }
}
