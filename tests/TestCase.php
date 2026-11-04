<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->ensureTestDatabase();
        $this->withoutVite();
    }

    /**
     * Autentica como o usuário configurado no phpunit.xml (ADMIN_USERNAME).
     */
    protected function entrarComoAdmin(): static
    {
        return $this->actingAs(Auth::createUserProvider('users')->retrieveById('admin'));
    }

    /**
     * Proteção extra: os testes só podem rodar em SQLite na memória,
     * nunca contra o PostgreSQL real configurado no .env.
     */
    protected function ensureTestDatabase(): void
    {
        $connection = DB::connection();

        if ($connection->getDriverName() !== 'sqlite' || $connection->getDatabaseName() !== ':memory:') {
            throw new RuntimeException('Os testes devem usar SQLite em memória. Verifique o phpunit.xml.');
        }
    }
}
