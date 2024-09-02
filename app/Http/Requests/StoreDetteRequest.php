<?php

namespace App\Http\Requests;

use App\Enums\ResponseStatus;
use App\Traits\ApiResponser;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class StoreDetteRequest extends FormRequest
{
    use ApiResponser;
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
    public function rules(): array
    {
        return [
            //
            'montant' => 'sometimes|numeric|min:0',
            'clientId' => 'required|exists:clients,id',
            'articles' => 'required|array',
            'articles.*.articleId' => 'required|exists:articles,id',
            'articles.*.qteVente' => 'required|integer|min:1',
            'articles.*.prixVente' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.required' => 'Le client est obligatoire',
            'articles.*.id.required' => 'L\'article est obligatoire',
            'articles.*.qte.required' => 'La quantité est obligatoire',
            'articles.*.qte.integer' => 'La quantité doit être un nombre entier',
            'articles.*.qte.min' => 'La quantité doit être superieur ou égale à 1',
            'articles.*.prix.required' => 'Le prix est obligatoire',
            'articles.*.prix.numeric' => 'Le prix doit être un nombre',
            'articles.*.prix.min' => 'Le prix doit être superieur ou égale à 0',
            'articles.*.id.exists' => 'L\'article n\'existe pas',
            'client_id.exists' => 'Le client n\'existe pas',
            'articles.required' => 'Les articles sont obligatoires',
            'articles.array' => 'Les articles doivent etre un tableau',
            'montant.numeric' => 'Le montant doit être un nombre',
            'montant.min' => 'Le montant doit être superieur ou égale à 0',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            $this->sendResponse(null, $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY,ResponseStatus::ECHEC)
        );
    }
}
