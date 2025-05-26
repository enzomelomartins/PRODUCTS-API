<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'file_path',
        'original_name',
        'mime_type',
        'size',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    // Accessor para obter a URL completa do anexo
    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }

    // Para incluir 'url' na serialização JSON/array
    protected $appends = ['url'];

    /**
     * Gera uma URL para a imagem redimensionada.
     */
    public function getResizedUrl(int $width, int $height): string
    {
        $resizedPath = "resized/{$width}x{$height}/" . basename($this->file_path);

        // Verifica se a imagem redimensionada já existe
        if (!Storage::disk('public')->exists($resizedPath)) {
            $this->resizeImage($width, $height, $resizedPath);
        }

        return Storage::disk('public')->url($resizedPath);
    }

    /**
     * Redimensiona a imagem usando a biblioteca GD.
     */
    private function resizeImage(int $width, int $height, string $resizedPath): void
    {
        $originalPath = Storage::disk('public')->path($this->file_path);
        $imageInfo = getimagesize($originalPath);

        if (!$imageInfo) {
            throw new \Exception('O arquivo não é uma imagem válida.');
        }

        [$originalWidth, $originalHeight] = $imageInfo;

        // Cria uma nova imagem com as dimensões especificadas
        $image = imagecreatefromstring(file_get_contents($originalPath));
        $resizedImage = imagecreatetruecolor($width, $height);

        // Redimensiona a imagem
        imagecopyresampled(
            $resizedImage,
            $image,
            0, 0, 0, 0,
            $width, $height,
            $originalWidth, $originalHeight
        );

        // Garante que o diretório para a imagem redimensionada exista
        $resizedFullPath = Storage::disk('public')->path($resizedPath);
        $resizedDir = dirname($resizedFullPath);

        if (!is_dir($resizedDir)) {
            mkdir($resizedDir, 0755, true); // Cria o diretório com permissões recursivas
        }

        // Salva a imagem redimensionada no disco
        imagejpeg($resizedImage, $resizedFullPath, 90);

        // Libera memória
        imagedestroy($image);
        imagedestroy($resizedImage);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}