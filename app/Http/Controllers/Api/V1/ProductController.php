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
use App\Http\Resources\ProductResource;

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
            $filters = $request->validated();
            $products = $this->productService->getFilteredProducts($filters);

            return response()->json([
                'data' => ProductResource::collection($products),
                'message' => 'Produtos listados com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->createProduct($request->validated());
        return response()->json([
            'data' => new ProductResource($product->load(['category', 'attachments'])),
            'message' => 'Produto criado com sucesso.'
        ], Response::HTTP_CREATED);
    }

    public function show(Product $product): JsonResponse
    {
        $product->load(['category', 'attachments']);
        return response()->json([
            'data' => new ProductResource($product),
            'message' => 'Produto obtido com sucesso.'
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $updated = $this->productService->updateProduct($product->id, $request->validated());

        if ($updated) {
            $product->refresh()->load(['category', 'attachments']);
            return response()->json([
                'data' => new ProductResource($product),
                'message' => 'Produto atualizado com sucesso.'
            ]);
        }

        return response()->json(['message' => 'Falha ao atualizar produto.'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function destroy(Product $product): JsonResponse
    {
        $deleted = $this->productService->deleteProduct($product->id);

        if ($deleted) {
            return response()->json(['message' => 'Produto deletado com sucesso.'], Response::HTTP_NO_CONTENT);
        }

        return response()->json(['message' => 'Falha ao deletar produto.'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}