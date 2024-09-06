<?php

namespace App\Jobs;

use App\Services\Contracts\IUploadService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Support\Str;

class StoreImageInCloud implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $tempPath;

    public function __construct($user, $tempPath)
    {
        $this->user = $user;
        $this->tempPath = $tempPath;
    }

    public function handle(IUploadService $uploadService)
    {
        try {
            // Vérifiez si le fichier temporaire existe
            if (!Storage::exists($this->tempPath)) {
                throw new \Exception('Le fichier temporaire n\'existe pas');
            }

            // Convertir le chemin temporaire en instance UploadedFile
            $filePath = Storage::path($this->tempPath);
            $file = new UploadedFile($filePath, basename($filePath));

            // Télécharger le fichier vers le cloud
            $fileUrl = $uploadService->upload($file);

            // Mettre à jour l'utilisateur avec l'URL du fichier
            $this->user->update(['photo' => $fileUrl]);

            // Supprimer le fichier temporaire
            Storage::delete($this->tempPath);
        } catch (\Exception $e) {

                // Vérifiez si le fichier temporaire existe
                if (!Storage::exists($this->tempPath)) {
                    throw new \Exception('Le fichier temporaire n\'existe pas');
                }

                $filePath = Storage::path($this->tempPath);
                $fileName = uniqid() . '.' . pathinfo($filePath, PATHINFO_EXTENSION);
                $path = 'public/users/' . $fileName;

                // Stocker l'image
                Storage::put($path, file_get_contents($filePath));

                // Obtenir l'URL publique
                $publicUrl = Storage::url($path);

                // Mettre à jour l'utilisateur avec l'URL du fichier
                $this->user->update(['photo' => $publicUrl]);

                // Supprimer le fichier temporaire
                Storage::delete($this->tempPath);
                return ;
            }
        }
    }

