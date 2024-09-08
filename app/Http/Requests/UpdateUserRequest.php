<?php

namespace App\Http\Requests;

use App\Enums\ResponseStatus;
use App\Rules\CustomPassword;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class UpdateUserRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            //

                'prenom' => 'sometimes|required|string|max:55|min:3',
                'nom' => 'sometimes|required|string|max:55|min:2',
                'login' => 'sometimes|required|string|max:55|min:2|unique:users,login',
                'password' => ['sometimes',new CustomPassword(),'confirmed'],
                'role' => 'sometimes|required|in:ADMIN,BOUTIQUIER',
        ];


    }

    public function failedValidation(Validator $validator)
{
    throw new HttpResponseException(
         $this->sendResponse(null, $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY,ResponseStatus::ECHEC)
    );
}
}
