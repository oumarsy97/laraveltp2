<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoyaltyCardMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $pdfPath;

    public function __construct($user, $pdfPath)
    {
        $this->user = $user;
        $this->pdfPath = $pdfPath;
    }

    public function build()
    {
        return $this->view('emails.loyalty')
                    ->attach($this->pdfPath, [
                        'as' => 'loyalty_card.pdf',
                        'mime' => 'application/pdf',
                    ]);
    }
}
