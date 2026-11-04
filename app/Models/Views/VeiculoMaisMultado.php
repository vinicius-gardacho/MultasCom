<?php

namespace App\Models\Views;

use App\Models\Concerns\IsReadOnlyView;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * View "vw_viniciusgardacho_veiculosMaisMultados": veículos com ao menos uma
 * multa, ordenados pela quantidade e pelo valor total das multas.
 */
#[Table(name: 'vw_viniciusgardacho_veiculosmaismultados', key: 'id_veiculo', timestamps: false)]
class VeiculoMaisMultado extends Model
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
            'qtd_multas' => 'integer',
            'valor_total_multas' => 'decimal:2',
        ];
    }
}
