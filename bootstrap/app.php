<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    ->withProviders([
        App\Providers\RepositoryServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        // ...
        $middleware->alias([
            'api.response' => \App\Http\Middleware\ApiResponseMiddleware::class, // Alias para usar em grupos de rotas
        ]);
        // Para aplicar globalmente em todas as rotas 'api':
        // $middleware->appendToGroup('api', [
        //     ApiResponseMiddleware::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Captura e renderiza exceções para rotas da API
        $exceptions->renderable(function (\Throwable $e, $request) {
            if ($request->is('api/*')) {
                $errors = \App\Exceptions\ApiExceptionHandler::handle($e);
                return response()->json($errors, $errors['status']);
            }
        });
    })->create();
