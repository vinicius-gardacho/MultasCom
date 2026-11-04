<?php

namespace App\Models\Concerns;

use LogicException;

/**
 * Models que representam views do banco são apenas para consulta.
 */
trait IsReadOnlyView
{
    use UsesExistingSchema;

    public function save(array $options = []): bool
    {
        throw new LogicException(static::class.' representa uma view do banco e é somente leitura.');
    }

    public function delete(): ?bool
    {
        throw new LogicException(static::class.' representa uma view do banco e é somente leitura.');
    }
}
