<?php
namespace App\Services;

use App\Enums\EtatEnum;
use App\Enums\ResponseStatus;
use App\Exceptions\ServiceException;
use App\Facades\UserRepositoryFacade;
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

    public function find( int $id)
    {
        return $this->clientRepository->find($id);
    }

    public function getClientById(int $id)
    {
        return $this->clientRepository->findWithUser($id);
    }

    public function createClient(array $data)
    {
        DB::beginTransaction(); // Démarrer une transaction
     try {
    $client = $this->clientRepository->create($data);

    if (isset($data['user']) && $data['user']!=null) {
        $userData = $data['user'];
        $userData['password'] = bcrypt($userData['password']);
        $user = UserRepositoryFacade::create($userData);
        $user->client()->save($client);
    }

    DB::commit(); // Terminer la transaction
    $client = $this->clientRepository->findWithUser($client->id);
    return $client;
} catch (Exception $e) {
    DB::rollBack();
    return new ServiceException($e->getMessage(), Response::HTTP_BAD_REQUEST, ResponseStatus::ECHEC);
}

}

    public function update(int $id, array $data)
    {
        return $this->clientRepository->update($id, $data);
    }

    public function delete(int $id)
    {
        return $this->clientRepository->delete($id);
    }

    public function findClientByTelephone(String $telephone)
    {
        return $this->clientRepository->findByTelephone($telephone);
    }

    public function filteredClients($active = null, $compte = null)
    {
        $query = $this->clientRepository->query();

        // Déterminer la valeur du filtre pour l'état actif/inactif des utilisateurs
        $etat = null;
        switch ($active) {
            case 'oui':
                $etat = EtatEnum::ACTIF->value;
                break;
            case 'non':
                $etat = EtatEnum::INACTIF->value;
                break;
        }

        // Déterminer la valeur du filtre pour le compte utilisateur
        $user = null;
        switch ($compte) {
            case 'oui':
                $user = 1;
                break;
            case 'non':
                $user = 0;
                break;
        }

        // Appliquer les filtres via le Repository
        if ($user !== null) {
            $query = $this->clientRepository->filterByUser($query, $user);
        }

        if ($etat !== null) {
            $query = $this->clientRepository->filterByEtat($query, $etat);
        }

        return $query->get();
    }

    public function findByTelephone( String $telephone)
    {
        try{
            return $this->clientRepository->findByTelephone($telephone);
        }
        catch (\Exception $e) {
            throw new ServiceException("Erreur dans le service: " . $e->getMessage(), $e->getCode());
        }
    }

}
