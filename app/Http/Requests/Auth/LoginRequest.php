<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Número de tentativas inválidas permitidas por minuto.
     */
    private const MAX_TENTATIVAS = 5;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'usuario' => ['required', 'string', 'max:100'],
            'senha' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * Valida as credenciais, com limite de tentativas por usuário e IP.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credenciais = ['usuario' => $this->string('usuario')->toString(), 'password' => $this->string('senha')->toString()];

        if (! Auth::attempt($credenciais)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'usuario' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * @throws ValidationException
     */
    private function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), self::MAX_TENTATIVAS)) {
            return;
        }

        event(new Lockout($this));

        $segundos = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'usuario' => __('auth.throttle', ['seconds' => $segundos, 'minutes' => ceil($segundos / 60)]),
        ]);
    }

    private function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('usuario')).'|'.$this->ip());
    }
}
