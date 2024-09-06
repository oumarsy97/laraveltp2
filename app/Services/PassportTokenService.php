<?php
// app/Services/PassportTokenService.php
namespace App\Services;

use App\Models\User;
use App\Services\Contracts\TokenServiceInterface;
use Laravel\Passport\Passport;

class PassportTokenService implements TokenServiceInterface
{
    public function createToken($user)
    {
        $user = User::withEncryptedClaims()->where('id', $user->id)->first(); // Utiliser le scope
        $token = $user->createToken('api-token', ['*'], [
            'encrypted_claims' => $user->getAttribute('encryptedClaims'),
        ])->accessToken;
        return $token;
    }

    public function createRefreshToken($user)
    {
        // Implémentation spécifique pour Passport si nécessaire

    }
}
