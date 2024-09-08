<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Services\Contracts\IUploadService;

class ImageUploaded
{
    use Dispatchable, SerializesModels;

    public $user;
    public $file;
    public $pdfPath;

    public function __construct(User $user, $file,$pdfPath)
    {
        $this->user = $user;
        $this->file = $file;
        $this->pdfPath = $pdfPath;
    }

}
