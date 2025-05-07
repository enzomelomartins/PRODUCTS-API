<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withProviders([
        App\Providers\RepositoryServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        // ...
        $middleware->alias([
            'api.response' => ApiResponseMiddleware::class, // Alias para usar em grupos de rotas
        ]);
        // Para aplicar globalmente em todas as rotas 'api':
        // $middleware->appendToGroup('api', [
        //     ApiResponseMiddleware::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
