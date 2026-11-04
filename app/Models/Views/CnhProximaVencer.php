<?php

namespace App\Models\Views;

use App\Models\Concerns\IsReadOnlyView;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * View "vw_viniciusgardacho_cnhProximasVencer": condutores com CNH vencida ou
 * com validade nos próximos 90 dias (dias_para_vencer negativo = já vencida).
 */
#[Table(name: 'vw_viniciusgardacho_cnhproximasvencer', key: 'id_condutor', timestamps: false)]
class CnhProximaVencer extends Model
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
            'validade_cnh' => 'date',
            'dias_para_vencer' => 'integer',
            'qtd_multas_condutor' => 'integer',
        ];
    }
}
