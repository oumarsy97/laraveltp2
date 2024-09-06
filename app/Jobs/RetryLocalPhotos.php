<?php

namespace App\Jobs;

use App\Services\Contracts\IUploadService;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class RetryLocalPhotos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $uploadService;

    public function __construct(IUploadService $uploadService)
    {
        $this->uploadService = $uploadService;

    }

    public function handle()
    {
        // Rechercher les utilisateurs ayant une photo en local

        $users = User::whereNotNull('photo')
                     ->where('photo', 'like', 'public/storage/users/%')
                     ->get();

        
        foreach ($users as $user) {
            $localPhotoPath = str_replace('public/storage/', 'storage/', $user->photo);

            if (Storage::exists($localPhotoPath)) {
                $file = new UploadedFile(Storage::path($localPhotoPath), basename($localPhotoPath));

                StoreImageInCloud::dispatch($user,$file);
            }
        }
    }
}
