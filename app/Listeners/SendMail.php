<?php

namespace App\Listeners;

use App\Events\ImageUploaded;
use App\Jobs\SendEmailJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendMail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ImageUploaded $event): void
    {
         SendEmailJob::dispatch($event->user, $event->pdfPath);
    }
}
