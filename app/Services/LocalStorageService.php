<?php

namespace App\Services;

use App\Services\Contracts\ILocalStorageService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile; // Utiliser la classe de Laravel

class LocalStorageService implements ILocalStorageService
{
    public function storeLocal(UploadedFile $file) : string
    {
        if (!$file instanceof UploadedFile) {
            throw new \Exception('Le fichier doit être une instance de UploadedFile');
        }

        // Enregistrez le fichier dans le répertoire 'public/users'
        $localPath = $file->store('public/users');

        // Retourner l'URL publique du fichier
        return Storage::url($localPath);
    }
}
