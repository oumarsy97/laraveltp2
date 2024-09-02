<?php

namespace App\Http\Requests;

use App\Enums\ResponseStatus;
use App\Rules\CustomPassword;
use App\Traits\ApiResponser;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class StoreUserRequest extends FormRequest
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

              'prenom' => 'required|string|max:55|min:3',
                'nom' => 'required|string|max:55|min:2',
                'login' => 'required|email|unique:users',
                'password' => ['required',new CustomPassword(),'confirmed'],
                'client_id' => 'required|exists:clients,id',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function failedValidation(Validator $validator)
    {

        throw new HttpResponseException(
            $this->sendResponse(null, $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY,ResponseStatus::ECHEC)
        );
    }
}
