<?php

namespace App\Services;

use App\Services\Contracts\IUploadService;
use Cloudinary\Cloudinary;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CloudinaryUploadService implements IUploadService
{
    protected $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key' => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
            'url' => [
                'secure' => true
            ]
        ]);
    }

    public function upload(UploadedFile $file): string
    {
        $result = $this->cloudinary->uploadApi()->upload($file->getRealPath());
        return $result['secure_url'];
    }
}
