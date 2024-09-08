<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Spatie\QueryBuilder\QueryBuilder;
use App\Enums\ResponseStatus;
use App\Facades\ClientRepositoryFacade;
use App\Facades\ServiceFacade;
use App\Facades\UserRepositoryFacade;
use App\Http\Requests\TelephoneRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Services\Contracts\IClientService;
use Exception;
use Illuminate\Http\Response;

class ClientController extends Controller
{
    protected IClientService $clientService;
        public function __construct(IClientService $clientService) {
            $this->clientService = $clientService;
        }

        public function index(Request $request) {
            $compte = $request->query('compte');
            $active = $request->query('active');
           $clients = $this->clientService->filteredClients($compte, $active);
        return [
            'data' => $clients,
            'status' => ResponseStatus::SUCCESS,
            'message' => 'Liste des clients',
            'code' => Response::HTTP_OK
        ];

}



    public function findByTelephone(TelephoneRequest $request)
{
    $telephone = $request->telephone;
    $client = $this->clientService->findByTelephone($telephone);
    if(!$client){
        return [
            'data' => null,
            'status' => ResponseStatus::ECHEC,
            'message' => 'Client non trouvé',
            'code' => Response::HTTP_NOT_FOUND
        ];
   }
    return  [
        'data' => $client,
        'status' => ResponseStatus::SUCCESS,
        'message' => 'Client retrouve avec succes',
        'code' => Response::HTTP_OK
    ];
}


public function show( $id)
{
     $data = $this->clientService->find($id);
     $client = new ClientResource($data);
    if(!$client){
        return [
            'data' => null,
            'status' => ResponseStatus::ECHEC,
            'message' => 'Client non trouvé',
            'code' => Response::HTTP_NOT_FOUND
        ];
    }
    return  [
        'data' => $client,
        'status' => ResponseStatus::SUCCESS,
        'message' => 'Client retrouve avec succes',
        'code' => Response::HTTP_OK
    ];
}

public function findUser ( $id){
    $client = $this->clientService->getClientById($id);
    if(!$client){
        return  [
            'data' => null,
            'status' => ResponseStatus::ECHEC,
            'message' => 'Client non trouvé',
            'code' => Response::HTTP_NOT_FOUND
        ];
    }
    return  [
        'data' => $client->user,
        'status' => ResponseStatus::SUCCESS,
        'message' => 'Client retrouve avec succes',
        'code' => Response::HTTP_OK
    ];
}

public function findDettes ($id){
    // try{
    // $client = ClientRepositoryFacade::findWithDettes($id);
    // if(!$client){
    //     return  $this->sendResponse(null,'Client non trouvé',Response::HTTP_NOT_FOUND,ResponseStatus::ECHEC);
    // }
    // return  $this->sendResponse($client, 'Client retrovée avec succes',Response::HTTP_OK,ResponseStatus::SUCCESS);
    // } catch (Exception $e) {
    //     return  $this->sendResponse(null,$e->getMessage(),Response::HTTP_BAD_REQUEST,ResponseStatus::ECHEC);
    // }
}



 public function store(ClientRequest $request)
{
    $data = $request->validated();
    // dd($data);
    $client = $this->clientService->createClient($data);
    if(!$client){
        return [
            'data' => null,
            'status' => ResponseStatus::ECHEC,
            'message' => 'Client non trouvé',
            'code' => Response::HTTP_NOT_FOUND
        ];
    }
    return  [
        'data' => $client,
        'status' => ResponseStatus::SUCCESS,
        'message' => 'Client ajoute avec succes',
        'code' => Response::HTTP_OK
    ];
 }

public function update(UpdateClientRequest $request, $id)
{
    try{
        $data = $request->validated();
        if ($data->fails()) {
            return [
                'data' => null,
                'status' => ResponseStatus::ECHEC,
                'message' => $data->errors(),
                'code' => Response::HTTP_NOT_FOUND
            ];
        }

        $client = $this->clientService->find($id);
        if(!$client){
            return [
                'data' => null,
                'status' => ResponseStatus::ECHEC,
                'message' => 'Client non trouvé',
                'code' => Response::HTTP_NOT_FOUND
            ];
        }

        $user = $client->user;
        if($request->has('user')){
            $validatedUserData = $data['user'];
            $validatedUserData['role'] = 'CLIENT';
            $validatedUserData['password'] = bcrypt($validatedUserData['password']);
            $idUser = $client->user->id;
            $user = UserRepositoryFacade::update($idUser, $validatedUserData);
        }
        return [
            'data' => $user,
            'status' => ResponseStatus::SUCCESS,
            'message' => 'Client mis à jour avec₀',
            'code' => Response::HTTP_OK
        ];
    } catch (Exception $e) {
        return [
            'data' => null,
            'status' => ResponseStatus::ECHEC,
            'message' => $e->getMessage(),
            'code' => Response::HTTP_NOT_FOUND
        ];
    }
}

public function destroy($id)
{
    $client = Client::findOrFail($id);
    if(!$client){
        return  $this->errorResponse(null,'Client not found', 404);
    }
    $client->delete();
    return  $this->successResponse($client, 'Client deleted successfully', 200);
}





//sort
public function sortbyTelephone()
{

        $clients = QueryBuilder::for(Client::class)
            ->allowedSorts(['telephone'])
            ->get();

        return $this->successResponse($clients, 'Clients trouvés', 200);
}

//filtrage et tri combinés
public function filterandsort()
{
        $clients = QueryBuilder::for(Client::class)
            ->allowedFilters(['telephone'])
            ->allowedSorts(['telephone'])
            ->get();

        return $this->successResponse($clients, 'Clients trouvés', 200);

}


}
