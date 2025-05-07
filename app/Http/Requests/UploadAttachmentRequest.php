<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadAttachmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // Máx 10MB, ajuste conforme necessário
        ];
    }

    public function messages(): array // Mensagens customizadas (opcional)
    {
        return [
            'image.required' => 'O campo imagem é obrigatório.',
            'image.image' => 'O arquivo deve ser uma imagem.',
            'image.mimes' => 'A imagem deve ser do tipo: jpeg, png, jpg, gif, webp.',
            'image.max' => 'A imagem não pode ser maior que 10MB.',
        ];
    }
}