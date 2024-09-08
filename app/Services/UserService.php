<?php
namespace App\Services;

use App\Enums\EtatEnum;
use App\Enums\RoleEnum;
use App\Events\ImageUploaded;
use App\Events\LoyaltyCardRequested;
use App\Exceptions\ServiceException;
use App\Jobs\GenerateAndSendLoyaltyCardJob;
use App\Models\User;
use App\Repositories\Contracts\IUserRepository;
use App\Services\Contracts\IUserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UserService implements IUserService
{
    private $userRepository;

    public function __construct(IUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index($role = null, $active = null)
    {
        $role_id = null;
        $etat = null;


        switch ($role) {

            case 'admin':
                $role_id = RoleEnum::ADMIN->value;
                break;
            case 'boutiquier':
                $role_id = RoleEnum::BOUTIQUIER->value;
                break;
            case 'client':
                $role_id = RoleEnum::CLIENT->value;
                break;
        }


        switch ($active) {
            case 'oui':
                $etat = EtatEnum::ACTIF->value;
                break;
            case 'non':
                $etat = EtatEnum::INACTIF->value;
                break;
        }

        if ($role_id != null && $etat != null) {
            return $this->userRepository->filter($role_id, $etat);
        }
        return $this->userRepository->getAll();

   }

    public function find(int $id)
    {
        return $this->userRepository->find($id);
    }

    public function create(array $data)
    {
        $data['password'] = bcrypt($data['password']);

       $user = $this->userRepository->create($data);
         // Déclencher l'événement ImageUploaded
         if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $tempPath = $data['photo']->store('temp');
            // Déclencher l'événement ImageUploaded
             event(new ImageUploaded($user, $tempPath, 'users'));
        }
        event(new LoyaltyCardRequested($user));
        return $user;


    }

    public function update( array $data, int $id)
    {
        return $this->userRepository->update($id, $data);
    }

    public function delete(int $id)
    {
        return $this->userRepository->delete($id);
    }


}
