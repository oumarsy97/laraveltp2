<?php
namespace App\Observers;

use App\Models\User;
use App\Jobs\StoreImageInCloud;

class UserObserver
{
    // Cette méthode reçoit un objet User et non une Closure
    public function created(User $user)
    {
        if (request()->hasFile('photo')) {
            $file = request()->file('photo');
            $tempPath = $file->store('temp');
            StoreImageInCloud::dispatch($user, $tempPath);
        }
    }

    public function updated(User $user)
    {
        if (request()->hasFile('photo')) {
            $file = request()->file('photo');
            $tempPath = $file->store('temp');
            StoreImageInCloud::dispatch($user, $tempPath);
        }
    }
}
