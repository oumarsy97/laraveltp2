<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Str;
use App\Enums\EnumRole;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Laravel\Sanctum\HasApiTokens as HasApiTokensTrait;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use HasFactory;
    use HasApiTokens;

    public function client(){
        return $this->hasOne(Client::class);
    }

    public function tokens()
    {
        return $this->hasMany(PersonalAccessToken::class);
    }

    // public function createToken($name, array $scopes = [])
    // {
    //     // Crée un accès token pour l'utilisateur
    //     $token = $this->tokens()->create([
    //         'name' => $name,
    //         'token' => hash('sha256', $plainTextToken = Str::random(40)),
    //         'abilities' => $scopes,
    //     ]);

    //     return new \Laravel\Passport\PersonalAccessTokenResult(
    //         $plainTextToken,
    //         $token
    //     );
    // }
    protected $fillable = [
        'prenom',
        'nom',
        'login',
        'password',
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
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}

