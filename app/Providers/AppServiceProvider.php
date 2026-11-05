<?php

namespace App\Providers;

use App\Auth\EnvUserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // A base de dados é compartilhada e já possui estrutura e dados próprios:
        // bloqueia migrate:fresh, migrate:refresh, migrate:reset e db:wipe.
        DB::prohibitDestructiveCommands();

        Auth::provider('env', fn ($app, array $config) => new EnvUserProvider(
            $app['hash'],
            $config['username'] ?? null,
            $config['password_hash'] ?? null,
        ));
    }
}
