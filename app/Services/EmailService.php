<?php

namespace App\Services;

use App\Services\Contracts\EmailServiceInterface;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;

class EmailService implements EmailServiceInterface
{
    public function sendEmailWithAttachment(string $to, string $subject, string $body, string $attachmentPath): void
    {
        Mail::raw($body, function ($message) use ($to, $subject, $attachmentPath) {
            $message->to($to)
                    ->subject($subject)
                    ->attach($attachmentPath);
        });
    }
}
