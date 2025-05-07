<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage; // Importar Storage

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

    // Accessor para obter a URL completa do anexo
    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }

    // Para incluir 'url' na serialização JSON/array
    protected $appends = ['url'];


    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}