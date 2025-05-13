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
        $query = Product::with(['category', 'attachments', 'tags']); // Carrega os relacionamentos

        // Filtro por tag
        if ($request->has('tag_id')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('id', $request->input('tag_id'));
            });
        }

        // Outros filtros (se necessário)
        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // Paginação
        $perPage = $request->input('per_page', 15);
        $products = $query->paginate($perPage);

        return response()->json([
            'data' => ProductResource::collection($products),
            'message' => 'Produtos obtidos com sucesso.'
        ]);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $data = $request->validated();
        $tags = $data['tags'] ?? [];
        unset($data['tags']);

        $product = $this->productService->createProduct($data);
        $product->tags()->sync($tags);

        return response()->json([
            'data' => new ProductResource($product->load(['category', 'attachments', 'tags'])),
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
        $data = $request->validated();
        $tags = $data['tags'] ?? [];
        unset($data['tags']);

        $updated = $this->productService->updateProduct($product->id, $data);
        $product->tags()->sync($tags);

        if ($updated) {
            $product->refresh()->load(['category', 'attachments', 'tags']);
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

    public function attachTags(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'tags' => 'required|array',
            'tags.*' => 'exists:tags,id', // Verifica se os IDs das tags existem na tabela 'tags'
        ]);

        // Sincroniza as tags com o produto (substitui as existentes)
        $product->tags()->sync($validated['tags']);

        return response()->json([
            'data' => $product->load('tags'), // Retorna o produto com as tags associadas
            'message' => 'Tags associadas ao produto com sucesso.'
        ]);
    }
}