<?php

use App\Http\Middleware\SecurityHeaders;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [SecurityHeaders::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Falhas de conexão ou consulta ao PostgreSQL: a exceção continua sendo
        // registrada no log, mas o usuário vê uma página amigável, sem SQL,
        // nomes de tabelas ou dados de conexão.
        $exceptions->render(function (QueryException $exception, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Banco de dados indisponível.'], 503);
            }

            return response()->view('errors.database', [], 503);
        });
    })->create();
