<?php

namespace App\Services;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class CategoryService
{
    protected CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAllCategories(): Collection
    {
        return $this->categoryRepository->getAll();
    }

    public function getCategoryById(int $id): ?Model
    {
        return $this->categoryRepository->findById($id);
    }

    public function createCategory(array $data): Model
    {
        return $this->categoryRepository->create($data);
    }

    public function updateCategory(int $id, array $data): bool
    {
        return $this->categoryRepository->update($id, $data);
    }

    public function deleteCategory(int $id): bool
    {
        $category = $this->categoryRepository->findById($id);

        if (!$category) {
            throw new \Exception('Categoria não encontrada.');
        }

        // Verifica se a categoria está sendo usada (exemplo: associada a produtos)
        if ($category->products()->exists()) {
            throw new \Exception('A categoria está sendo usada e não pode ser apagada.');
        }

        return $this->categoryRepository->delete($id);
    }
}