<?php

namespace App\Http\Requests;

use App\Enums\ResponseStatus;
use App\Traits\ApiResponser;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class StoreArticleRequest extends FormRequest
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
            'libelle' => 'required|min:3|max:55|unique:articles,libelle',
            'qteStock' => 'required|numeric|gt:0',
            'prix' => 'required|numeric|gt:0',
        ];
    }

    public function failedValidation(Validator $validator)
{

    throw new HttpResponseException(
        $this->sendResponse(null, $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY,ResponseStatus::ECHEC)
    );
}
}
