<?php
// app/Services/MailService.php

namespace App\Services;

use Illuminate\Support\Facades\Mail;

class MailService
{
    public function sendLoyaltyCardEmail($user, $pdfContent)
    {
        Mail::send([], [], function($message) use ($user, $pdfContent) {
            $message->to($user->email)
                    ->subject('Votre Carte de Fidélité')
                    ->attachData($pdfContent, 'loyalty_card.pdf', [
                        'mime' => 'application/pdf',
                    ])
                    ->setBody('Voici votre carte de fidélité.');
        });
    }
}
