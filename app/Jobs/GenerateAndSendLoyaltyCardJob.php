<?php
namespace App\Jobs;

use App\Models\User;
use App\Services\QrCodeService;
use App\Services\PdfService;
use App\Services\MailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateAndSendLoyaltyCardJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;

    }

    public function handle(QrCodeService $qrCodeService, PdfService $pdfService, MailService $mailService)
    {
        // 1. Générer le QR code pour l'utilisateur
         $qrCode = $qrCodeService->generateQrCode($this->user->id);

        // 2. Générer la carte de fidélité au format PDF
          $pdf = $pdfService->generateLoyaltyCard($this->user, $qrCode);


        // 3. Envoyer le PDF par mail
        //  $mailService->sendLoyaltyCardEmail($this->user, $pdf);
    }
}
