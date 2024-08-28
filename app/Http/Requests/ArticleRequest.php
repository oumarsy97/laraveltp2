<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function attributes()
    {
        return [
            'libelle' => 'libellé',
            'qteStock' => 'quantité en stock',
            'prix' => 'prix',
        ];
    }

    public function rules()
    {
        return [
            'libelle' => 'required|string|max:255',
            'qteStock' => 'required|integer|min:0',
            'prix' => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'libelle.required' => 'Le libellé est requis.',
            'libelle.string' => 'Le libellé doit être une chaîne de caractères.',
            'libelle.max' => 'Le libellé ne peut pas dépasser 255 caractères.',
            'qteStock.required' => 'La quantité en stock est requise.',
            'qteStock.integer' => 'La quantité en stock doit être un entier.',
            'qteStock.min' => 'La quantité en stock ne peut pas être négative.',
            'prix.required' => 'Le prix est requis.',
            'prix.numeric' => 'Le prix doit être un nombre.',
            'prix.min' => 'Le prix ne peut pas être négatif.',
        ];
    }
}
