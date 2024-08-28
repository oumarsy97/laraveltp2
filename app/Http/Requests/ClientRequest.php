<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidPhoneNumber;

class ClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */


    public function rules()
    {
        $rules = [
            'adresse' => 'required|string|max:255',
            'user' => 'nullable|array',
            'telephones' => ['required', new ValidPhoneNumber],
        ];



        if ($this->isMethod('patch') || $this->isMethod('put')) {
            // Règles spécifiques pour la mise à jour
            $rules['telephone'] = 'sometimes|numeric|unique:users,telephone,' . $this->user()->id;
            $rules['adresse'] = 'sometimes|string|max:255';
            $rules['user'] = 'nullable|array';
        }

        return $rules;

    }

    public function messages()
    {
        return [
            'telephone.required' => 'Le numéro de téléphone est requis.',
            'adresse.required' => 'L\'adresse est requise.',
            'user_id.exists' => 'L\'utilisateur spécifié n\'existe pas.',
        ];
    }
}
