<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Defina sua lógica de autorização aqui
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O campo name é obrigatório.',
            'name.string' => 'O campo name deve ser uma string.',
            'name.max' => 'O campo name deve ter no máximo 255 caracteres.',
            'name.unique' => 'O campo name já existe.',
            'description.string' => 'O campo description deve ser uma string.',
        ];
    }
}