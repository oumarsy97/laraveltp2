<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'login' => [
                'sometimes',
                'required',
                'email',
                'unique:users,login'
            ],
            'password' => [
                'sometimes',
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&]/',
            ],
            'confirm_password' => 'sometimes|required_with:password|same:password',
            'role' => 'sometimes|required|in:ADMIN,BOUTIQUIER',

        ];
    }
}
