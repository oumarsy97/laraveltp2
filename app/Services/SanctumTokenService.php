<?php
// app/Services/SanctumTokenService.php
namespace App\Services;

use App\Models\User;
use App\Services\Contracts\TokenServiceInterface;
use Laravel\Sanctum\Sanctum;

class SanctumTokenService implements TokenServiceInterface
{
    public function createToken($user)
    {
        $user = User::withEncryptedClaims()->where('id', $user->id)->first(); // Utiliser le scope
        $token = $user->createToken('api-token', ['*'], [
            'encrypted_claims' => $user->getAttribute('encryptedClaims'),
        ])->plainTextToken;
        return $token;
    }

    public function createRefreshToken($user)
    {
        // Sanctum ne supporte pas les refresh tokens nativement
    }
}

