<?php

namespace App\Repositories\Contracts;

interface CategoryRepositoryInterface extends BaseRepositoryInterface
{
    public function query(): \Illuminate\Database\Eloquent\Builder;
}