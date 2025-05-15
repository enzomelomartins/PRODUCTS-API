<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|integer|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'status' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'O produto deve ter uma categoria associada.',
            'category_id.integer' => 'O campo category_id deve ser um número inteiro.',
            'category_id.exists' => 'O campo category_id deve existir na tabela categories.',
            'name.required' => 'O campo name é obrigatório.',
            'name.string' => 'O campo name deve ser uma string.',
            'name.max' => 'O campo name deve ter no máximo 255 caracteres.',
            'description.string' => 'O campo description deve ser uma string.',
            'price.required' => 'O campo price é obrigatório.',
            'price.numeric' => 'O campo price deve ser um número.',
            'price.min' => 'O campo price deve ser maior ou igual a 0.',
            'status.boolean' => 'O campo status deve ser verdadeiro ou falso.',
        ];
    }
}