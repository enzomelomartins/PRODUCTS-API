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
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(IndexProductRequest $request): JsonResponse
    {
        $query = Product::with(['category', 'attachments', 'tags']);

        // Filtros
        if ($request->has('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // Paginação
        $perPage = $request->input('per_page', 15);
        $products = $query->paginate($perPage);

        if ($products->isEmpty()) {
                return response()->json([
                    'message' => 'Nenhum produto encontrado.'
                ]);
            }

        return response()->json([
            'message' => 'Produtos obtidos com sucesso.',
            'data' => ProductResource::collection($products),
        ]);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $data = $request->validated();
        
        $product = $this->productService->createProduct($data);

        if ($request->has('tags')) {
            $product->tags()->sync($request->input('tags'));
        }

        return response()->json([
            'message' => 'Produto criado com sucesso.',
            'data' => new ProductResource($product->load(['category', 'attachments', 'tags'])),
        ], Response::HTTP_CREATED);
    }

    public function show(Product $product): JsonResponse
    {
        $product->load(['category', 'attachments']);
        return response()->json([
            'message' => 'Produto obtido com sucesso.',
            'data' => new ProductResource($product),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $data = $request->validated();
        $updated = $this->productService->updateProduct($product->id, $data);

        if ($request->has('tags')) {
            $product->tags()->sync($request->input('tags'));
        }

        if ($updated) {
            $product->refresh()->load(['category', 'attachments', 'tags']);
            return response()->json([
                'message' => 'Produto atualizado com sucesso.',
                'data' => new ProductResource($product),
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