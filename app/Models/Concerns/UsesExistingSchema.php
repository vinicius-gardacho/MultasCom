<?php

namespace App\Models\Concerns;

/**
 * Qualifica o nome da tabela com o schema configurado em DB_SCHEMA quando a
 * conexão é PostgreSQL (ex.: "viniciusgardacho_trabalho_db.multa").
 *
 * O search_path definido na conexão não é suficiente quando o acesso passa
 * pelo pooler do Neon (PgBouncer em modo transação), pois o "SET search_path"
 * pode não persistir entre transações. Com o nome qualificado, todas as
 * consultas apontam para o schema correto em qualquer cenário.
 */
trait UsesExistingSchema
{
    public function getTable(): string
    {
        $table = parent::getTable();

        if (str_contains($table, '.')) {
            return $table;
        }

        $connection = $this->getConnection();

        if ($connection->getDriverName() !== 'pgsql') {
            return $table;
        }

        $searchPath = explode(',', (string) $connection->getConfig('search_path'));
        $schema = trim($searchPath[0], " \"'");

        return $schema === '' || $schema === 'public' ? $table : "{$schema}.{$table}";
    }
}
