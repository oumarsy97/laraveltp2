<?php

namespace App\Jobs;

use App\Mail\LoyaltyCardMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $pdfPath;

    /**
     * Create a new job instance.
     *
     * @param  $user
     * @param  string  $pdfPath
     * @return void
     */
    public function __construct($user, $pdfPath)
    {
        $this->user = $user;
        $this->pdfPath = $pdfPath;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Envoie de l'email avec la pièce jointe (PDF)
        dd($this->user, $this->pdfPath);
        Mail::to($this->user->login)->send(new LoyaltyCardMail($this->user, $this->pdfPath));
    }
}
