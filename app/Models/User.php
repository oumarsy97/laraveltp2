<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Str;
use App\Enums\EnumRole;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Model
{
    use Notifiable;
    use HasFactory;
    use HasApiTokens;

    public function createToken($name, array $scopes = [])
    {
        // Crée un accès token pour l'utilisateur
        $token = $this->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken = Str::random(40)),
            'abilities' => $scopes,
        ]);

        return new \Laravel\Passport\PersonalAccessTokenResult(
            $plainTextToken,
            $token
        );
    }
    protected $fillable = [
        'prenom',
        'nom',
        'login',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];
    // Cast pour l'énumération de rôle
    protected function role(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value, // Retourne la valeur telle quelle
            set: fn ($value) => in_array($value, EnumRole::getValues()) ? $value : EnumRole::CLIENT,
        );
    }
}

