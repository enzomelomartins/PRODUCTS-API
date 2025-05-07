<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts;
use App\Repositories\Eloquent;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            Contracts\CategoryRepositoryInterface::class,
            Eloquent\EloquentCategoryRepository::class
        );

        $this->app->bind(
            Contracts\ProductRepositoryInterface::class,
            Eloquent\EloquentProductRepository::class
        );

        $this->app->bind(
            Contracts\AttachmentRepositoryInterface::class,
            Eloquent\EloquentAttachmentRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}