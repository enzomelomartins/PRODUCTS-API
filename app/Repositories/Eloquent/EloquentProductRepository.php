<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Collection; // Adicionado

class EloquentProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function getAllWithRelations(array $relations): Collection
    {
        return $this->model->with($relations)->get();
    }

    public function query(): \Illuminate\Database\Eloquent\Builder
    {
        return $this->model->newQuery();
    }
}