<?php

namespace App\Auth;

use Illuminate\Auth\GenericUser;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Hashing\Hasher;

/**
 * Provedor de autenticação com um único usuário definido no .env
 * (ADMIN_USERNAME e ADMIN_PASSWORD_HASH).
 *
 * O banco existente não possui tabela de usuários e não deve ser alterado,
 * por isso as credenciais de acesso ao sistema ficam fora dele.
 */
class EnvUserProvider implements UserProvider
{
    public function __construct(
        private readonly Hasher $hasher,
        private readonly ?string $username,
        #[\SensitiveParameter] private readonly ?string $passwordHash,
    ) {}

    public function retrieveById($identifier): ?Authenticatable
    {
        return $this->configurado() && hash_equals($this->username, (string) $identifier)
            ? $this->usuario()
            : null;
    }

    public function retrieveByToken($identifier, #[\SensitiveParameter] $token): ?Authenticatable
    {
        // "Lembrar-me" não é suportado: não há onde persistir o token.
        return null;
    }

    public function updateRememberToken(Authenticatable $user, #[\SensitiveParameter] $token): void
    {
        //
    }

    public function retrieveByCredentials(#[\SensitiveParameter] array $credentials): ?Authenticatable
    {
        return $this->retrieveById($credentials['usuario'] ?? '');
    }

    public function validateCredentials(Authenticatable $user, #[\SensitiveParameter] array $credentials): bool
    {
        return $this->configurado()
            && $this->hasher->check((string) ($credentials['password'] ?? ''), $user->getAuthPassword());
    }

    public function rehashPasswordIfRequired(Authenticatable $user, #[\SensitiveParameter] array $credentials, bool $force = false): void
    {
        // O hash fica no .env e não pode ser atualizado pela aplicação.
    }

    private function configurado(): bool
    {
        return filled($this->username)
            && filled($this->passwordHash)
            && password_get_info($this->passwordHash)['algo'] === PASSWORD_BCRYPT;
    }

    private function usuario(): GenericUser
    {
        return new GenericUser([
            'id' => $this->username,
            'name' => $this->username,
            'password' => $this->passwordHash,
            'remember_token' => null,
        ]);
    }
}
