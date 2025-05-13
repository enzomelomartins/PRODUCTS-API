<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Attachment;
use App\Services\AttachmentService;
use App\Http\Requests\UploadAttachmentRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response; // Importar Response
use Illuminate\Http\Request; // Importar Request
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
     * Upload multiple attachments for a specific product.
     */
    public function storeMultiple(Request $request, Product $product): JsonResponse
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'file|mimes:jpeg,png,jpg,gif,webp|max:2048', // Valida cada arquivo
        ]);

        try {
            $files = $request->file('images');
            $attachments = $this->attachmentService->uploadMultipleAttachmentsForProduct($product, $files);

            // Adiciona a URL do thumbnail para cada anexo
            $attachmentsWithThumbnails = array_map(function ($attachment) {
                return [
                    'id' => $attachment->id,
                    'file_name' => $attachment->original_name,
                    'size' => $attachment->size,
                    'mime_type' => $attachment->mime_type,
                    'url' => $attachment->url,
                    'thumbnail_resized' => $attachment->getResizedUrl(150, 150), // Exemplo: thumbnail 150x150
                ];
            }, $attachments);

            return response()->json([
                'data' => $attachmentsWithThumbnails,
                'message' => 'Imagens enviadas com sucesso.',
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Falha no upload das imagens: ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
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
        return response()->json(['message' => 'Falha ao deletar anexo.'], Response::HTTP_ACCEPTED); // Ou 500 se falhar por outra razão
    }

    /**
     * Retorna a URL de uma imagem redimensionada.
     */
    public function resize(Request $request, Attachment $attachment): JsonResponse
    {
        $request->validate([
            'width' => 'required|integer|min:1',
            'height' => 'required|integer|min:1',
        ]);

        $width = $request->input('width');
        $height = $request->input('height');

        try {
            $resizedUrl = $this->attachmentService->getResizedAttachmentUrl($attachment, $width, $height);

            return response()->json([
                'success' => true,
                'data' => [
                    'url' => $resizedUrl,
                    'width' => $width,
                    'height' => $height,
                ],
                'message' => 'Imagem redimensionada com sucesso.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao redimensionar a imagem: ' . $e->getMessage(),
            ], 500);
        }
    }
}