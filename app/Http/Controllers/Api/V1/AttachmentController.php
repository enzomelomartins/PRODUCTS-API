<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Attachment;
use App\Services\AttachmentService;
use App\Http\Requests\UploadAttachmentRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response; // Importar Response
use Exception; // Importar Exception


class AttachmentController extends Controller
{
    protected AttachmentService $attachmentService;

    public function __construct(AttachmentService $attachmentService)
    {
        $this->attachmentService = $attachmentService;
    }

    /**
     * Upload a new attachment for a specific product.
     */
    public function store(UploadAttachmentRequest $request, Product $product): JsonResponse
    {
        try {
            $file = $request->file('image');
            $attachment = $this->attachmentService->uploadAttachmentForProduct($product, $file);
            return response()->json(['data' => $attachment, 'message' => 'Anexo enviado com sucesso.'], Response::HTTP_CREATED);
        } catch (Exception $e) {
            // Log::error('Upload failed: ' . $e->getMessage()); // Adicione log aqui
            return response()->json(['message' => 'Falha no upload do anexo: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display a specific attachment.
     * (Não muito útil para uma API JSON, a menos que você queira os metadados)
     * A URL do anexo já está no modelo Product.
     */
    public function show(Attachment $attachment): JsonResponse
    {
         // O accessor 'url' no modelo Attachment já fornece o link.
        return response()->json(['data' => $attachment, 'message' => 'Detalhes do anexo obtidos com sucesso.']);
    }


    /**
     * Remove the specified attachment from storage and database.
     */
    public function destroy(Attachment $attachment): JsonResponse
    {
        $deleted = $this->attachmentService->deleteAttachment($attachment->id);
        if ($deleted) {
            return response()->json(null, Response::HTTP_NO_CONTENT);
        }
        return response()->json(['message' => 'Falha ao deletar anexo.'], Response::HTTP_NOT_FOUND); // Ou 500 se falhar por outra razão
    }
}