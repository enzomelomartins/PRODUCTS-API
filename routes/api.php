<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\AttachmentController;

// Em Laravel 11, o prefixo 'api' e o middleware 'api' não são aplicados por padrão como antes.
// Precisamos adicioná-los explicitamente ou usar o bootstrap/app.php para configurar isso.

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1', 'middleware' => \App\Http\Middleware\ApiResponseMiddleware::class], function () { // Aplicando o middleware de resposta
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('products', ProductController::class);

    // Rotas específicas para anexos de um produto
    Route::post('/products/{product}/attachments', [AttachmentController::class, 'store'])->name('products.attachments.store');
    // Para deletar um anexo específico (não o produto)
    Route::delete('/attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');
    Route::get('/attachments/{attachment}', [AttachmentController::class, 'show'])->name('attachments.show');
});