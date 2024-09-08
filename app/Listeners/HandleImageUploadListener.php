<?php

namespace App\Listeners;

use App\Events\ImageUploaded;
use App\Jobs\StoreImageInCloud;

class HandleImageUploadListener
{
    public function handle(ImageUploaded $event)
    {
        // dd("ok");
        // Dispatcher le job pour stocker l'image dans le cloud
         StoreImageInCloud::dispatch($event->user, $event->file);
         
    }
}
