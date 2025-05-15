<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\{
    NotFoundHttpException,
    MethodNotAllowedHttpException,
    HttpExceptionInterface
};
use Throwable;

class ApiExceptionHandler
{
    public static function handle(Throwable $e): array
    {
        $status = 500;
        $errors = null;

        if ($e instanceof NotFoundHttpException && $e->getPrevious() instanceof ModelNotFoundException) {
            $message = 'ID não encontrado.';
        } else {
            $message = match (true) {
                $e instanceof ValidationException => 'Os dados fornecidos são inválidos.',
                $e instanceof ModelNotFoundException => 'ID não encontrado.',
                $e instanceof NotFoundHttpException => 'Esta rota não existe.',
                $e instanceof MethodNotAllowedHttpException => 'Este método HTTP não é permitido.',
                $e instanceof HttpExceptionInterface => $e->getMessage() ?: 'Erro HTTP.',
                default => 'Erro interno no servidor.',
            };
        }

        $status = match (true) {
            $e instanceof ValidationException => 422,
            $e instanceof ModelNotFoundException => 404,
            $e instanceof NotFoundHttpException => 404,
            $e instanceof MethodNotAllowedHttpException => 405,
            $e instanceof HttpExceptionInterface => $e->getStatusCode(),
            default => 500,
        };

        if ($e instanceof ValidationException) {
            $errors = $e->errors();
        }

        return [
            'status' => $status,
            'errors' => [
                'mensagem' => $message,
                'erro' => $e->getMessage(),
                ...(is_array($errors) && count($errors) > 1 ? ['detalhes' => $errors] : []),
                ]
        ];
    }
}
