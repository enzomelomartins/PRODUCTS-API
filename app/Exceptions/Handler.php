<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse; // Adicionar
use Illuminate\Validation\ValidationException; // Adicionar
use Symfony\Component\HttpKernel\Exception\HttpException; // Adicionar
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Custom JSON error responses for API routes
        $this->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*') || $request->expectsJson()) { // Verifica se é uma rota de API
                return $this->handleApiException($request, $e);
            }
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'message' => 'Os dados fornecidos são inválidos.',
                'errors' => $exception->errors(),
            ], 422);
        }

        return parent::render($request, $exception);
    }

    private function handleApiException($request, Throwable $exception): JsonResponse
    {
        $statusCode = $this->getStatusCode($exception);
        $response = [
            'success' => false,
            'message' => $exception->getMessage() ?: 'Erro interno do servidor.',
        ];

        if ($exception instanceof ValidationException) {
            $response['errors'] = $exception->errors();
            $response['message'] = 'Os dados fornecidos são inválidos.'; // Mensagem genérica para validação
        }

        // Adicionar mais detalhes em ambiente de desenvolvimento
        if (config('app.debug')) {
            $response['exception'] = get_class($exception);
            $response['trace'] = $exception->getTraceAsString(); // Cuidado com isso em produção
        }

        return response()->json($response, $statusCode);
    }

    private function getStatusCode(Throwable $exception): int
    {
        if ($exception instanceof HttpException) {
            return $exception->getStatusCode();
        }
        if ($exception instanceof ValidationException) {
            return 422;
        }
        // Outras exceções comuns podem ser mapeadas aqui
        // if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
        //     return 401;
        // }
        // if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
        //     return 404;
        // }

        // Default para 500 se não for uma HttpException ou outra conhecida
        return method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500;
    }
}