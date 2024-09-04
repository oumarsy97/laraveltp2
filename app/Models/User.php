<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Str;
use App\Enums\EnumRole;
use App\Events\ImageUploaded;
use App\Jobs\SendLoyaltyCard;
use App\Jobs\StoreImageInCloud;
use App\Mail\CarteFideliteMail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Laravel\Sanctum\HasApiTokens as HasApiTokensTrait;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class User extends Authenticatable
{
    use SoftDeletes;
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
        'photo'
    ];

    protected $hidden = [
        'password',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];
    // Cast pour l'énumération de rôle
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    // Définir un scope pour ajouter des données cryptées au token
    public function scopeWithEncryptedClaims(Builder $query)
    {
        return $query->get()->map(function ($user) {
            $user->setAttribute('encryptedClaims', base64_encode(json_encode([
                'user_id' => $user->id,
                'login' => $user->login,
                'role' => $user->role->libelle,
            ])));
            return $user;
        });
    }


    protected static function boot()
    {
        parent::boot();



        static::created(function ($user) {
            if (request()->hasFile('photo')) {
                $file = request()->file('photo');

                // Sauvegarder le fichier temporairement
                $tempPath = $file->store('temp');

                StoreImageInCloud::dispatch($user, $tempPath);
            }


            $text ="".$user->login;
   $qrCodePath = '../app/qrcodes/test_qrcode.png';
        QrCode::format('png')->size(300)->generate($text, $qrCodePath);
        $pdfContent = Pdf::loadView('pdf.loyalty_card', ['user' => $user, 'qrCodePath' => $qrCodePath])->output();
    $pdfPath = '/home/seydina/LARAVEL/tp2T/resources/views/pdf/loyalty_card.'. Str::random(10) . '.pdf';
    file_put_contents($pdfPath, $pdfContent);
     Mail::to($user->login)->send(new CarteFideliteMail($user, $pdfPath));
     //supprimer le pdf
     unlink($pdfPath);


        });
    }
}

