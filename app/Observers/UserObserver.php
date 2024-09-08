<?php

namespace App\Observers;

use App\Jobs\StoreImageInCloud;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UserObserver
{
    public function created(User $user)
    {
        // Vérifier si le fichier est présent dans la requête
        $file = request()->file('photo');
        if ($file instanceof UploadedFile) {
            // Créer un chemin temporaire pour le fichier
            $tempPath = $file->store('temp');

            // Sauvegarder temporairement le fichier
            $filePath = Storage::path($tempPath);

            // Dispatcher le Job avec le chemin temporaire du fichier
             StoreImageInCloud::dispatch($user, $filePath);

        }
    }
}
