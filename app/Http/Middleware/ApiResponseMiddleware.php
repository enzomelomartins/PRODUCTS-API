<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response; // Importar Response

class ApiResponseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($response instanceof JsonResponse && $response->isSuccessful() && isset($response->getData()->data)) {
            $originalData = $response->getData();
            $formattedResponse = [
                'success' => true,
                'data' => $originalData->data,
                'message' => $originalData->message ?? null,
            ];
            // Se houver paginação, mescla as informações de paginação
            if (isset($originalData->meta)) {
                $formattedResponse['meta'] = $originalData->meta;
            }
            if (isset($originalData->links)) {
                $formattedResponse['links'] = $originalData->links;
            }

            return response()->json($formattedResponse, $response->getStatusCode());
        }

        return $response;
    }
}