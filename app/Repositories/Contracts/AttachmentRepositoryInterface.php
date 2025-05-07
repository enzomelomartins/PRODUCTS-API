<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use App\Models\Attachment; // Adicionado

interface AttachmentRepositoryInterface extends BaseRepositoryInterface
{
    public function createForProduct(Product $product, UploadedFile $file, string $filePath): Attachment;
    public function findByPath(string $filePath): ?Attachment;
}