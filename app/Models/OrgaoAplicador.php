<?php

namespace App\Models;

use App\Models\Concerns\UsesExistingSchema;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Tabela "orgao_aplicador": órgãos responsáveis por aplicar as multas.
 */
#[Table(name: 'orgao_aplicador', key: 'id_orgao', timestamps: false)]
class OrgaoAplicador extends Model
{
    use UsesExistingSchema;

    /**
     * Multas aplicadas pelo órgão (multa.orgao_id).
     */
    public function multas(): HasMany
    {
        return $this->hasMany(Multa::class, 'orgao_id', 'id_orgao');
    }
}
