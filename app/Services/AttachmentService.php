<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Attachment;
use App\Repositories\Contracts\AttachmentRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // Importar Str
use Exception; // Importar Exception

class AttachmentService
{
    protected AttachmentRepositoryInterface $attachmentRepository;

    public function __construct(AttachmentRepositoryInterface $attachmentRepository)
    {
        $this->attachmentRepository = $attachmentRepository;
    }

    public function uploadAttachmentForProduct(Product $product, UploadedFile $file): Attachment
    {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $fileName = Str::uuid() . '.' . $extension;
        $directory = "products/{$product->id}/attachments";

        // Tenta armazenar o arquivo
        $path = $file->storeAs($directory, $fileName, 'public');

        if (!$path) {
            throw new Exception("Não foi possível armazenar o arquivo.");
        }

        
        $product = $this->attachmentRepository->createForProduct($product, $file, $path);
        return $product;
    }

    /**
     * Upload multiple attachments for a specific product.
     */
    public function uploadMultipleAttachmentsForProduct(Product $product, array $files): array
    {
        $attachments = [];

        foreach ($files as $file) {
            $attachments[] = $this->uploadAttachmentForProduct($product, $file);
        }

        return $attachments;
    }

    public function deleteAttachment(int $attachmentId): bool
    {
        $attachment = $this->attachmentRepository->findById($attachmentId);
        if ($attachment) {
            // Deleta o arquivo físico
            Storage::disk('public')->delete($attachment->file_path);
            // Deleta o registro do banco
            return $this->attachmentRepository->delete($attachmentId);
        }
        return false;
    }

    public function getAttachmentById(int $id): ?Attachment
    {
        return $this->attachmentRepository->findById($id);
    }

    public function getResizedAttachmentUrl(Attachment $attachment, int $width, int $height): string
    {
        return $attachment->getResizedUrl($width, $height);
    }
}