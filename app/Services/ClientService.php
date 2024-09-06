<?php
namespace App\Services;

use App\Enums\EtatEnum;
use App\Enums\ResponseStatus;
use App\Exceptions\RepositoryException;
use App\Exceptions\ServiceException;
use App\Http\Requests\TelephoneRequest;
use App\Models\Client;
use App\Models\User;
use App\Services\Contracts\IClientService;
use App\Repositories\Contracts\IClientRepository;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ClientService implements IClientService
{
    protected $clientRepository;

    public function __construct(IClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function getAllClients()
    {
        return $this->clientRepository->getAll();
    }

    public function getClientById(int $id)
    {
        return $this->clientRepository->find($id);
    }

    public function createClient(array $data)
    {
        DB::beginTransaction(); // Démarrer une transaction
 try {
    $client = Client::create($data);

    if (isset($data['user']) && $data['user']!=null) {
        $userData = $data['user'];
        $userData['password'] = bcrypt($userData['password']);
        $user = User::create($userData);
        $user->client()->save($client);
    }

    DB::commit(); // Terminer la transaction
    $client = Client::with('user')->find($client->id);
    return [

        'status' => ResponseStatus::SUCCESS,
        'message' => 'Client enregistré avec succes',
        'data' => $client,
        'code' => Response::HTTP_OK
    ];
} catch (Exception $e) {
    DB::rollBack();
    return new ServiceException($e->getMessage(), Response::HTTP_BAD_REQUEST, ResponseStatus::ECHEC);
}

}

    public function updateClient(int $id, array $data)
    {
        return $this->clientRepository->update($id, $data);
    }

    public function deleteClient(int $id)
    {
        return $this->clientRepository->delete($id);
    }

    public function filteredClients(?string $compte, ?string $active) {
        $query = Client::query();
        $user = null;
        $etat = null;

        // Déterminer la valeur du filtre pour l'état actif/inactif des utilisateurs
        switch ($active) {
            case 'oui':
                $etat = EtatEnum::ACTIF->value;
                break;
            case 'non':
                $etat = EtatEnum::INACTIF->value;
                break;
        }

        // Déterminer la valeur du filtre pour le compte utilisateur
        switch ($compte) {
            case 'oui':
                $user = 1;
                break;
            case 'non':
                $user = 0;
                break;
        }

        if($user == 1){
            $query->whereNotNull('user_id')->with('user');
        }
        if($user != null && $user == 0){
            $query->whereNull('user_id');
        }
        if ($etat === EtatEnum::ACTIF->value) {
            $query->whereHas('user', function ($query) {
                $query->where('etat', '=', EtatEnum::ACTIF->value);
            });
            $query->with('user');
        } elseif ($etat === EtatEnum::INACTIF->value) {
            $query->whereHas('user', function ($query) {
                $query->where('etat', '=', EtatEnum::INACTIF->value);
            });
            $query->with('user');
        }

        return $query->get();
    }

    public function findByTelephone( String $telephone)
    {
        try{

            return  Client::first();
        }
        catch (\Exception $e) {
            throw new ServiceException("Erreur dans le service: " . $e->getMessage(), $e->getCode());
        }
    }

}
