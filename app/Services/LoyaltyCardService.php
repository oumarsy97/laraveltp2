<?php

namespace App\Services;

use App\Exceptions\ServiceException;
use App\Mail\CarteFideliteMail;
use App\Mail\LoyaltyCardMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Services\Contracts\QRCodeGeneratorInterface;
use App\Services\Contracts\LoyaltyCardServiceInterface;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class LoyaltyCardService implements LoyaltyCardServiceInterface
{



    public function sendLoyaltyCard($user, $pdf)
    {
        try{
            $text ="".$user->login;
                $qrCodePath = '../app/qrcodes/test_qrcode.png';
                QrCode::format('png')->size(300)->generate($text, $qrCodePath);
                $pdfContent = Pdf::loadView('pdf.loyalty_card', ['user' => $user, 'qrCodePath' => $qrCodePath])->output();
                $pdfPath = '/home/seydina/LARAVEL/tp2T/resources/views/pdf/loyalty_card.'. Str::random(10) . '.pdf';
              
                file_put_contents($pdfPath, $pdfContent);
                 Mail::to($user->login)->send(new CarteFideliteMail($user, $pdfPath));


            // Envoyer l'email
            // Mail::to($user->login)->send(new LoyaltyCardMail($user, $pdfPath));
            unlink($pdfPath);

        } catch (\Exception $e) {
            return new ServiceException("Erreur dans le service: " . $e->getMessage(), $e->getCode());
        }
    }
}
