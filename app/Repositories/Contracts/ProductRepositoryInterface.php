<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection; // Adicionado para getAllWithRelations

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    // Exemplo de método específico
    public function getAllWithRelations(array $relations): Collection;
}