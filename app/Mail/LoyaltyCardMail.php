namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoyaltyCardMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $pdf;

    public function __construct(User $user, $pdf)
    {
        $this->user = $user;
        $this->pdf = $pdf;
    }

    public function build()
    {
        return $this->view('emails.loyalty_card')
                    ->subject('Votre carte de fidélité')
                    ->attachData($this->pdf, 'loyalty_card.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
