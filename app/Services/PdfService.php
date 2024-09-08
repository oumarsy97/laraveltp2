<?php
// app/Services/PdfService.php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;

class PdfService
{
    public function generateLoyaltyCard($user, $qrCode)
    {
        $pdf = PDF::loadView('pdf.loyalty_card', [
            'user' => $user,
            'qrCode' => $qrCode
        ]);
        //sauvegarde du pdf
        $pdfPath = storage_path('public/qrcodes/qrcode_' . $user->id . '.pdf');
        $pdf->save($pdfPath);


        return $pdf->output(); // Renvoie le contenu PDF
    }
}
