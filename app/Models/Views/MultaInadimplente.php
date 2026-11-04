<?php

namespace App\Models\Views;

use App\Models\Concerns\IsReadOnlyView;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * View "vw_viniciusgardacho_multasInadimplentes": multas sem pagamento, com
 * status "Pendente" ou "Vencida" e vencimento anterior à data atual.
 */
#[Table(name: 'vw_viniciusgardacho_multasinadimplentes', key: 'id_multa', timestamps: false)]
class MultaInadimplente extends Model
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
            'valor' => 'decimal:2',
            'data_vencimento' => 'date',
            'dias_em_atraso' => 'integer',
        ];
    }
}
