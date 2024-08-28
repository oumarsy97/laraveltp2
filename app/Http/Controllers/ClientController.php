<?php

namespace App\Http\Controllers;

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
use App\Rules\CustomPassword;

class ClientController extends Controller
{
    use ApiResponser;
    //
    public function index()
{
    // $clients = Client::with('user')->paginate(10);

    $clients = QueryBuilder::for(Client::class)
            ->allowedFilters(['telephone'])
            ->get();

        return  ApiResponser::sendResponse($clients, 'Clients trouvés',200, ResponseStatus::SUCCESS);
    // return $this->successResponse($clients, 'Clients trouvés', 200);

    if(!$clients){
        return  $this->errorResponse(' clients non trouvés', 404);
    }

    return $this->successResponse($clients, 'Clients trouvés', 200);
 }


 public function store(ClientRequest $request)
{
    DB::beginTransaction(); // Démarrer une transaction

 try {

    $data = $request->validated();
    if($data->fails()) {
        return response()->json(['errors' => $data->errors()], 422);
    }

    if ($request->has('user')) {
        $userData = $data['user'];
        $userData['role'] ='CLIENT';
        try {
            $validator = Validator::make($userData, [
                'prenom' => 'required|string|max:55|min:3',
                'nom' => 'required|string|max:55|min:2',
                'login' => 'required|email|unique:users',
                // 'password' => 'required|string|min:8|regex:/[A-Z]/|regex:/[a-z]/|regex:/[0-9]/|regex:/[@$!%*?&]/',
                'password' => ['required', new CustomPassword],
                'confirm_password' => 'required|same:password',
            ]);
            if ($validator->fails()) {
                return $this->errorResponse($validator->errors(), 422);
            }

            $validatedUserData = $validator->validated(); // Obtenir les données validées

        } catch (ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        }

        // Créer l'utilisateur et ajouter son ID aux données du client
         $validatedUserData['password'] = bcrypt($validatedUserData['password']);
        $user = User::create($validatedUserData);
        $data['user_id'] = $user->id;
    }

    // Créer le client avec ou sans user_id
    $client = Client::create($data)->load('user');

    DB::commit(); // Terminer la transaction

    return $this->successResponse($client, 'Client créé avec succès', 201);

} catch (ValidationException $e) {
    DB::rollBack();
    // return  ApiResponser::sendResponse($e->errors(), 422);
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


public function show($id)
{
    $client = Client::with('user')->findOrFail($id);
    return new ClientResource($client);
    if(!$client){
        return  $this->errorResponse(null,'Client not found', 404);
    }
    return  $this->successResponse($client, 'Client retrieved successfully', 200);
}
//filtre client
public function filterbyTelephone(Request $request)
{

        $clients = QueryBuilder::for(Client::class)
            ->allowedFilters(['telephone'])
            ->get();

        return $this->successResponse($clients, 'Clients trouvés', 200);
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
