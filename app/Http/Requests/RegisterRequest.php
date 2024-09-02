<?php

namespace App\Http\Requests;

use App\Enums\ResponseStatus;
use App\Rules\CustomPassword;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use App\Traits\ApiResponser;

class RegisterRequest extends FormRequest
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
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'login' => 'required|string|email|max:255|unique:users',
            'role' => 'required|in:ADMIN,BOUTIQUIER',
            'password' => ['required',new CustomPassword(),'confirmed'],
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
