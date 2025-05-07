<?php

namespace App\Repositories\Eloquent;

use App\Models\Attachment;
use App\Models\Product;
use App\Repositories\Contracts\AttachmentRepositoryInterface;
use Illuminate\Http\UploadedFile;

class EloquentAttachmentRepository extends BaseRepository implements AttachmentRepositoryInterface
{
    public function __construct(Attachment $model)
    {
        parent::__construct($model);
    }

    public function createForProduct(Product $product, UploadedFile $file, string $filePath): Attachment
    {
        return $this->model->create([
            'product_id' => $product->id,
            'file_path' => $filePath,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);
    }

    public function findByPath(string $filePath): ?Attachment
    {
        return $this->model->where('file_path', $filePath)->first();
    }
}