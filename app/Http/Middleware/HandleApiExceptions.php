<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Throwable;
use App\Exceptions\ApiExceptionHandler;

class HandleApiExceptions
{
    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        } catch (Throwable $e) {
            $handled = ApiExceptionHandler::handle($e);

            return response()->json([
                'status' => $handled['status'],
                'message' => $handled['message'],
                'errors' => $handled['errors'],
                'debug' => $handled['debug'],
            ], $handled['status']);
        }
    }
}
