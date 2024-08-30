<?php
namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidPhoneNumber implements ValidationRule
{
    /**
     * Exécuter la règle de validation.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // et commence par l'un des préfixes autorisés
        if (!preg_match('/^(78|76|70|77|75)\d{7}$/', $value)) {
            $fail('Le :attribute doit être un numéro de téléphone valide (9 chiffres commençant par 78, 76, 70, 77 ou 75).');
        }
    }
}
