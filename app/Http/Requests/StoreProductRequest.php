<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Autoriser tout le monde pour ce projet
    }

    public function rules(): array
    {
        return [
            'name'           => 'required|string|max:255|unique:products,name',
            'category_id'    => 'required|exists:categories,id',
            'price'          => 'required|numeric|min:0',
            'quantity_stock' => 'required|integer|min:0',
            'description'    => 'nullable|string|min:10',
        ];
    }

    // Optionnel : Personnaliser les messages d'erreur
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du produit est indispensable !',
            'name.unique'   => 'Ce nom de produit existe déjà en stock.',
            'price.numeric' => 'Le prix doit être un nombre valide.',
        ];
    }
}