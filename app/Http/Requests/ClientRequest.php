<?php

namespace App\Http\Requests;

use App\Enums\ResponseStatus;
use App\Rules\CustomPassword;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ValidPhoneNumber;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Traits\ApiResponser;

class ClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /***
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */


    public function rules(): array
    {
        $rules = [
            'telephone' => ['required', new ValidPhoneNumber(),'unique:clients,telephone'],
            'adresse' => 'required|string|max:55',
            'surnom' => 'required|string|max:55|unique:clients,surnom',
            'user_id' => 'nullable|exists:users,id',
            'user' => 'nullable|array',
            'user.nom' => 'required_with:user,|string|max:255',
            'user.prenom' => 'required_with:user,|string|max:255',
            'user.login' => 'required_with:user,|email|unique:users,login',
            'user.password' => ['required_with:user,',new CustomPassword(),'confirmed'],
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        return $rules;
    }

    //
    public function messages()
    {
        return [
            'telephone.required' => 'Le numéro de téléphone est requis.',
            'adresse.required' => 'L\'adresse est requise.',
            'user_id.exists' => 'L\'utilisateur spécifié n\'existe pas.',
            'telephone.required' => 'Le numéro de téléphone est requis.',
            'adresse.required' => 'L\'adresse est requise.',
            'user.nom.required_if' => 'Le nom de l\'utilisateur est requis si l\'utilisateur est fourni.',
            'user.prenom.required_if' => 'Le prenom de l\'utilisateur est requis si l\'utilisateur est fourni.',
            'user.login.required_if' => 'L\'email de l\'utilisateur est requis si l\'utilisateur est fourni.',
            'user.password.required_if' => 'Le mot de passe de l\'utilisateur est requis si l\'utilisateur est fourni.',
            'user.password.confirmed' => 'Le mot de passe de l\'utilisateur doit être confirme.',
        ];
    }

    public function failedValidation(Validator $validator)
{
    throw new HttpResponseException(
        ApiResponser::sendResponse(null, $validator->errors(), 422,ResponseStatus::ECHEC)
    );
}
}
