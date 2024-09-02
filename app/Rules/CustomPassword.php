<?php
namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Password;
use \Illuminate\Support\Facades\Validator;

class CustomPassword implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Définir les règles du mot de passe
        $passwordRules = Password::min(5) // Minimum de 8 caractères
            ->mixedCase()  // Au moins une majuscule
            ->letters()    // Au moins une lettre
            ->numbers()    // Au moins un chiffre
            ->symbols()
            ->uncompromised()
            ;   // Au moins un caractère spécial
              // Définir les messages d'erreur personnalisés
        $messages = [
            'password.min' => 'Le mot de passe doit contenir au moins :min caractères.',
            'password.mixed_case' => 'Le mot de passe doit contenir au moins une lettre majuscule.',
            'password.letters' => 'Le mot de passe doit contenir au moins une lettre.',
            'password.numbers' => 'Le mot de passe doit contenir au moins un chiffre.',
            'password.symbols' => 'Le mot de passe doit contenir au moins un caractère spécial.',
            'password.uncompromised' => 'Le mot de passe a été compromis dans une violation de données.',
        ];


        // Application des règles de validation du mot de passe
        $validator = Validator::make(
            request()->all(),
            [$attribute => $passwordRules],
            $messages
        );

        // Si la validation échoue, retourne les messages d'erreur
        if ($validator->fails()) {
            // $fail($validator->errors()->first($attribute));
            $errors = $validator->errors()->all();
            $fail(implode(' ', $errors));


        }
    }
}
