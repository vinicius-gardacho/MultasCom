<?php

namespace App\Models;

use App\Models\Concerns\UsesExistingSchema;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Tabela "status_multa": domínio de status possíveis para uma multa.
 */
#[Table(name: 'status_multa', key: 'id_status', timestamps: false)]
class StatusMulta extends Model
{
    use UsesExistingSchema;

    /**
     * Multas com este status (multa.status_id).
     */
    public function multas(): HasMany
    {
        return $this->hasMany(Multa::class, 'status_id', 'id_status');
    }
}
