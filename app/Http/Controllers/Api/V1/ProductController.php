<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductService;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\IndexProductRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(IndexProductRequest $request): JsonResponse
    {
        try {
            $filters = $request->validated(); // Obtém os filtros validados
            $products = $this->productService->getFilteredProducts($filters);

            return response()->json(['data' => $products, 'message' => 'Produtos listados com sucesso.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->createProduct($request->validated());
        return response()->json(['data' => $product->load(['category', 'attachments']), 'message' => 'Produto criado com sucesso.'], Response::HTTP_CREATED);
    }

    public function show(Product $product): JsonResponse
    {
        // Carrega relações se não foram carregadas via route model binding (o padrão do Laravel não carrega automaticamente)
        $product->load(['category', 'attachments']);
        return response()->json(['data' => $product, 'message' => 'Produto obtido com sucesso.']);
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $updated = $this->productService->updateProduct($product->id, $request->validated());
        if ($updated) {
            $product->refresh()->load(['category', 'attachments']);
            return response()->json(['data' => $product, 'message' => 'Produto atualizado com sucesso.']);
        }
        return response()->json(['message' => 'Falha ao atualizar produto.'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function destroy(Product $product): JsonResponse
    {
        // Lógica para deletar anexos físicos associados ao produto antes de deletar o produto, se necessário.
        // O AttachmentService pode ter um método para isso, ou pode ser feito aqui.
        // Por simplicidade, onDelete('cascade') na migration de attachments já remove os registros.
        // Mas os arquivos físicos não são removidos automaticamente.
        foreach ($product->attachments as $attachment) {
            // Idealmente, isso estaria em ProductService ou AttachmentService
            \Illuminate\Support\Facades\Storage::disk('public')->delete($attachment->file_path);
        }

        $deleted = $this->productService->deleteProduct($product->id);
        if ($deleted) {
            return response()->json(null, Response::HTTP_NO_CONTENT);
        }
        return response()->json(['message' => 'Falha ao deletar produto.'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}