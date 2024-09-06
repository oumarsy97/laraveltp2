<?php
// app/Mail/CarteFideliteMail.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CarteFideliteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $filePath;

    public function __construct($user, $filePath)
    {
        $this->user = $user;
        $this->filePath = $filePath;
    }

    public function build()
    {
        return $this->subject('Votre carte de fidélité')
                    ->view('emails.carte_fidelite')
                    ->attach($this->filePath);
    }
}

