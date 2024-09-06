<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\RetryLocalPhotos;
use App\Services\Contracts\IUploadService;

class RetryLocalPhotosCommand extends Command
{
    protected $signature = 'photos:retry';
    protected $description = 'Retry uploading local photos to the cloud';

    protected $uploadService;

    public function __construct(IUploadService $uploadService)
    {
        parent::__construct();
        $this->uploadService = $uploadService;
    }

    public function handle()
    {
       
        RetryLocalPhotos::dispatch($this->uploadService);
        $this->info('Retry local photos job dispatched.');
    }
}
