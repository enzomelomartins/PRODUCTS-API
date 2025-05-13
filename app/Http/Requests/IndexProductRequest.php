<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexProductRequest extends FormRequest
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
            'tag_id' => 'nullable|integer|exists:tags,id', // Valida o ID da tag
            'category_id' => 'nullable|integer|exists:categories,id',
            'name' => 'nullable|string|max:255',
            'status' => 'nullable|boolean',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0|gte:min_price',
            'per_page' => 'nullable|integer|min:1|max:100', // Limite de itens por página
        ];
    }

    /**
     * Mensagens de erro personalizadas.
     */
    public function messages(): array
    {
        return [
            'tag_id.integer' => 'O ID da tag deve ser um número inteiro.',
            'tag_id.exists' => 'A tag selecionada não foi encontrada.',
            'category_id.integer' => 'O ID da categoria deve ser um número inteiro.',
            'category_id.exists' => 'A categoria selecionada não foi encontrada.',
            'name.string' => 'O nome deve ser uma string válida.',
            'name.max' => 'O nome não pode exceder 255 caracteres.',
            'status.boolean' => 'O status deve ser verdadeiro ou falso.',
            'min_price.numeric' => 'O preço mínimo deve ser um número válido.',
            'min_price.min' => 'O preço mínimo não pode ser negativo.',
            'max_price.numeric' => 'O preço máximo deve ser um número válido.',
            'max_price.min' => 'O preço máximo não pode ser negativo.',
            'max_price.gte' => 'O preço máximo deve ser maior ou igual ao preço mínimo.',
            'per_page.integer' => 'O número de itens por página deve ser um número inteiro.',
            'per_page.min' => 'O número de itens por página deve ser pelo menos 1.',
            'per_page.max' => 'O número de itens por página não pode exceder 100.',
        ];
    }
}