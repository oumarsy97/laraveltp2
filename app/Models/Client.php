<?php

namespace App\Models;

use App\Jobs\SendEmailJob;
use App\Models\Scopes\TelephoneScope;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;


class Client extends Model
{
    use SoftDeletes;
    use HasFactory;
    use Notifiable;
    protected $fillable = [
        'telephone',
        'adresse',
        'surnom'
    ];


    public function dettes()
    {
        return $this->hasMany(Dette::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    protected static function booted()
    {
        $telephone = request()->input('telephone');
        if ($telephone) {
            static::addGlobalScope(new TelephoneScope($telephone));
        }

        static::created(function ($client) {
             if($client->user!=null){
                $user = $client->user;
                $text ="".$user->login;
                $qrCodePath = '../app/qrcodes/test_qrcode.png';
                QrCode::format('png')->size(300)->generate($text, $qrCodePath);
                $pdfContent = Pdf::loadView('pdf.loyalty_card', ['user' => $user, 'qrCodePath' => $qrCodePath])->output();
                $pdfPath = '/home/seydina/LARAVEL/tp2T/resources/views/pdf/loyalty_card.'. Str::random(10) . '.pdf';
                file_put_contents($pdfPath, $pdfContent);
                $pdfPath = storage_path('app/qrcodes/qrcode_' . $client->id . '.pdf');
                SendEmailJob::dispatch($user, $pdfPath);
                unlink($pdfPath);
             }
        });
    }



    // public function paiements()
    // {
    //     return $this->hasMany(Paiement::class);
    // }

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'user_id'
    ];
}
