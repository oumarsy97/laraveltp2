<?php

namespace App\Http\Controllers;

use App\Enums\EtatEnum;
use App\Http\Requests\ClientRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Models\User;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Spatie\QueryBuilder\QueryBuilder;
use App\Enums\ResponseStatus;
use App\Facades\ClientFacade;
use App\Facades\ServiceFacade;
use App\Http\Requests\TelephoneRequest;
use App\Services\Contracts\IClientService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

class ClientController extends Controller
{
    use ApiResponser;
    protected IClientService $clientService;

        public function __construct(IClientService $clientService) {
            $this->clientService = $clientService;
        }

        public function index(Request $request) {
            $compte = $request->query('compte');
            $active = $request->query('active');
           $clients = $this->clientService->filteredClients($compte, $active);
        return $clients;

        }



    public function findByTelephone(TelephoneRequest $request)
{
     return ServiceFacade::findByTelephone( $request->telephone);
}


public function show( $id)
{
     $data = Client::with('user')->find($id);
     $client = new ClientResource($data);
    if(!$client){
        return  $this->errorResponse(null,'Client not found', 404);
    }
    return  $this->sendResponse($client, 'Client retrieved successfully',Response::HTTP_OK,ResponseStatus::SUCCESS);
}

public function findUser ( $id){
    $client = Client::with('user')->find($id);
    if(!$client){
        return  $this->errorResponse(null,'Client non trouvé', 404);
    }
    return  $this->sendResponse($client, 'Client retrovée avec succes',Response::HTTP_OK,ResponseStatus::SUCCESS);
}

public function findDettes ($id){
    try{
    $client = Client::with('dettes')->find($id);
    if(!$client){
        return  $this->sendResponse(null,'Client non trouvé',Response::HTTP_NOT_FOUND,ResponseStatus::ECHEC);
    }
    return  $this->sendResponse($client, 'Client retrovée avec succes',Response::HTTP_OK,ResponseStatus::SUCCESS);
    } catch (Exception $e) {
        return  $this->sendResponse(null,$e->getMessage(),Response::HTTP_BAD_REQUEST,ResponseStatus::ECHEC);
    }
}



 public function store(ClientRequest $request)
{
    DB::beginTransaction(); // Démarrer une transaction
 try {
    $data = $request->validated();
    $client = Client::create($data);

    if ($request->has('user')) {
        $userData = $request->input('user');
        $userData['password'] = bcrypt($userData['password']);
        $user = User::create($userData);
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('users', 'public');
            $validator['photo'] = $path;
        }
        $user->client()->save($client);
    }

    DB::commit(); // Terminer la transaction
    $client = Client::with('user')->find($client->id);
    return $this->sendResponse($client, 'Client creé avec succès', Response::HTTP_OK,ResponseStatus::SUCCESS);

} catch (ValidationException $e) {
    DB::rollBack();
     return  ApiResponser::sendResponse($e->errors(), $e->getMessage(),Response::HTTP_UNPROCESSABLE_ENTITY,ResponseStatus::ECHEC);
}
 }

public function update(ClientRequest $request, $id)
{
    try{

        try {
            $data = $request->validated();
            if ($data->fails()) {
                return response()->json(['errors' => $data->errors()], 422);
            }
        } catch (ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        }

        $client = Client::findOrFail($id)->load('user');
        $user = $client->user;
        if(!$client){
             return  ApiResponser::sendResponse(null,'Client non trouvé',ResponseStatus::ECHEC, 404);
        }
        if ($request->has('user')) {
            $userData = $data['user'];
            $userData['role'] ='CLIENT';
            try {
                $validator = Validator::make($userData, [
                    'prenom' => 'something|string|max:55|min:3',
                    'nom' => 'something|string|max:55|min:2',
                    'login' => 'something|email|unique:users,login,'.$client->user->id,
                    'password' => 'something|string|min:8|regex:/[A-Z]/|regex:/[a-z]/|regex:/[0-9]/|regex:/[@$!%*?&]/',
                    'confirm_password' => 'something|same:password',
                ]);
                if ($validator->fails()) {
                    return $this->errorResponse($validator->errors(), 422);
                }

                $validatedUserData = $validator->validated(); // Obtenir les données validées

            } catch (ValidationException $e) {
                return $this->errorResponse($e->errors(), 422);
            }

            // update l'utilisateur et ajouter son ID aux données du client
             $validatedUserData['password'] = bcrypt($validatedUserData['password']);
             $user->update($validatedUserData);
            $data['user_id'] = $user->id;
        }

        // update le client avec ou sans user_id
         $client->update($data)->load('user');
        return  $this->successResponse($client, 'Client mis à jour avec succès', 200);
    } catch (ValidationException $e) {
        return $this->errorResponse($e->errors(), 422);
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
