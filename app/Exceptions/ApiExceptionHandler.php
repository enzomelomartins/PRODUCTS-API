<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ApiExceptionHandler
{
    public static function handle(Throwable $e)
    {
        $status = 500;
        $message = 'Erro interno no servidor';
        $errors = null;

        switch (true) {
            case $e instanceof ValidationException:
                $status = 422;
                $message = 'Erro de validação';
                $errors = $e->errors();
                break;

            case $e instanceof ModelNotFoundException:
                $status = 404;
                $model = class_basename($e->getModel());
                $message = "$model não encontrado.";
                break;

            case $e instanceof NotFoundHttpException:
                $status = 404;
                $message = 'Rota não encontrada.';
                break;

            case $e instanceof MethodNotAllowedHttpException:
                $status = 405;
                $message = 'Método HTTP não permitido.';
                break;

            case $e instanceof HttpExceptionInterface:
                $status = $e->getStatusCode();
                $message = $e->getMessage() ?: $message;
                break;

            default:
                // erro genérico
                break;
        }

        return [
            'success' => false,
            'status' => $status,
            'message' => $message,
            'errors' => $errors,
            'debug' => app()->environment('local') ? [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                // 'trace' => $e->getTrace()
            ] : null,
        ];
    }
}
