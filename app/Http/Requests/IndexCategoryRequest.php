<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexCategoryRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta solicitação.
     */
    public function authorize(): bool
    {
        return true; // Ajuste conforme necessário para permissões específicas
    }

    /**
     * Regras de validação para a solicitação.
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'status' => 'nullable|boolean',
            'per_page' => 'nullable|integer|min:1|max:100', // Limite de itens por página
        ];
    }

    /**
     * Mensagens de erro personalizadas.
     */
    public function messages(): array
    {
        return [
            'name.string' => 'O nome deve ser uma string válida.',
            'name.max' => 'O nome não pode exceder 255 caracteres.',
            'status.boolean' => 'O status deve ser verdadeiro ou falso.',
            'per_page.integer' => 'O número de itens por página deve ser um número inteiro.',
            'per_page.min' => 'O número de itens por página deve ser pelo menos 1.',
            'per_page.max' => 'O número de itens por página não pode exceder 100.',
        ];
    }
}