<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\CategoryService;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Requests\IndexCategoryRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(IndexCategoryRequest $request): JsonResponse
    {
        try {
            $filters = $request->validated(); // Obtém os filtros validados
            $categories = $this->categoryService->getFilteredCategories($filters);

            if ($categories->isEmpty()) {
                return response()->json([
                    'message' => 'Nenhuma categoria encontrada.'
                ]);
            }

            return response()->json([
                'data' => CategoryResource::collection($categories),
                'message' => 'Categorias listadas com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->createCategory($request->validated());
        return response()->json([
            'data' => new CategoryResource($category),
            'message' => 'Categoria criada com sucesso.'
        ], Response::HTTP_CREATED);
    }

    public function show(Category $category): JsonResponse // Route model binding
    {
        return response()->json([
            'data' => CategoryResource::make($category),
            'message' => 'Categoria obtida com sucesso.']);
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $updated = $this->categoryService->updateCategory($category->id, $request->validated());
        if ($updated) {
            $category->refresh();
            return response()->json([
                'data' => new CategoryResource($category),
                'message' => 'Categoria atualizada com sucesso.']);
        }
        return response()->json(['message' => 'Falha ao atualizar categoria.'], Response::HTTP_INTERNAL_SERVER_ERROR); // Ou 404 se não encontrar
    }

    public function destroy(Category $category): JsonResponse
    {
        $deleted = $this->categoryService->deleteCategory($category->id);
        if ($deleted) {
            return response()->json([
                'message' => 'Categoria deletada com sucesso.'
            ], Response::HTTP_OK);
        }
        return response()->json(['message' => 'Falha ao deletar categoria.'], Response::HTTP_INTERNAL_SERVER_ERROR); // Ou 404
    }
}