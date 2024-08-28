<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class CustomPassword implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //
        // Utiliser la façade Password pour les contraintes de validation
        $passwordRules = Password::min(8) // Minimum de 8 caractères
            ->mixedCase()  // Au moins une majuscule
            ->letters()    // Au moins une lettre
            ->numbers()    // Au moins un chiffre
            ->symbols()    // Au moins un caractère spécial
            ->uncompromised();  // Pas de mot de passe compromis

        // Application des règles de validation du mot de passe
        $validator = Validator::make([$attribute => $value], [
            $attribute => $passwordRules,
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $fail(implode(' ', $errors));
        }
    }
}
