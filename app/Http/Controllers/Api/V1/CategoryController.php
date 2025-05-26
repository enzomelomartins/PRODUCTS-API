<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\CategoryService;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Requests\IndexCategoryRequest;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

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
            $filters = $request->validated();
            $categories = Category::with('subcategories')
                ->whereNull('parent_id')
                ->get();

            return response()->json([
                'data' => CategoryResource::collection($categories),
                'message' => 'Categorias listadas com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
            $category = Category::create($request->validated());
            
            if ($category->parent_id) {
            $parent = Category::with('subcategories')->find($category->parent_id);

            return response()->json([
                'data' => new CategoryResource($parent),
                'message' => 'Categoria criada com sucesso.'
            ], 201);
        }

            return response()->json([
                'data' => new CategoryResource($category),
                'message' => 'Categoria criada com sucesso.'
            ], 201);
        }

    public function show(Category $category): JsonResponse
    {
        $category = Category::find($category->id);
        return response()->json([
            'data' => new CategoryResource($category->load('subcategories')),
            'message' => 'Categoria obtida com sucesso.'
        ]);
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $updated = $this->categoryService->updateCategory($category->id, $request->validated());
        if ($updated) {
            $category->refresh();
            return response()->json([
                'data' => new CategoryResource($category),
                'message' => 'Categoria atualizada com sucesso.'
            ]);
        }
        return response()->json(['message' => 'Falha ao atualizar categoria.'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function destroy(Category $category): JsonResponse
    {
        $deleted = $this->categoryService->deleteCategory($category->id);
        if ($deleted) {
            return response()->json(null, Response::HTTP_NO_CONTENT);
        }
        return response()->json(['message' => 'Falha ao deletar categoria.'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}