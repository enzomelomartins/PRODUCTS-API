<?php

namespace App\Services;

use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class ProductService
{
    protected ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getAllProducts(): Collection
    {
        return $this->productRepository->getAllWithRelations(['category', 'attachments']);
    }

    public function getProductById(int $id): ?Model
    {
        return $this->productRepository->findById($id, ['*'], ['category', 'attachments']);
    }

    public function createProduct(array $data): Model
    {
        return $this->productRepository->create($data);
    }

    public function updateProduct(int $id, array $data): bool
    {
        return $this->productRepository->update($id, $data);
    }

    public function deleteProduct(int $id): bool
    {
        return $this->productRepository->delete($id);
    }

    public function getFilteredProducts(array $filters): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = $this->productRepository->query();

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['min_price'])) {
        $query->where('price', '>=', $filters['min_price']);
    }

        if (!empty($filters['max_price'])) {
        $query->where('price', '<=', $filters['max_price']);
        }

        return $query->with(['category', 'attachments'])->paginate($filters['per_page'] ?? 10);
    }
}