<?php

namespace App\Http\Controllers;

use App\Enums\EtatEnum;
use App\Enums\ResponseStatus;
use App\Enums\RoleEnum;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\Client;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    use ApiResponser;
    //

/**
 * @OA\Get(
 *     path="/api/users",
 *     summary="Get list of users",
 *     tags={"users"},
 *     @OA\Response(
 *         response=200,
 *         description="Successful response",
 *     ),
 * )
 */

    public function index (Request $request)
{
    try {
        $role = $request->query('role');
        $active = $request->query('active');

        $role_id = null;
        $etat = null;

        switch ($role) {

            case 'admin':
                $role_id = RoleEnum::ADMIN;
                break;
            case 'boutiquier':
                $role_id = RoleEnum::BOUTIQUIER;
                break;
            case 'client':
                $role_id = RoleEnum::CLIENT;
                break;
        }

        switch ($active) {
            case 'oui':
                $etat = EtatEnum::ACTIF;
                break;
            case 'non':
                $etat = EtatEnum::INACTIF;
                break;

        }
        $query = User::query();

        if ($role_id !== null) {
            $query->where('role_id', $role_id);
        }

        if ($etat !== null) {
            $query->where('etat', $etat);
        }

        $users = $query->get();

        return $this->sendResponse($users, 'liste des utilisateurs', Response::HTTP_OK, ResponseStatus::SUCCESS);
    } catch (ValidationException $e) {
        return $this->errorResponse($e->errors(), 422);
    }
}

//pour creer un compte utilisateur boutiquier ou admin
public function store(RegisterRequest $request)
{
    if ($request->user()->cannot('admin')) {
        return $this->sendResponse(null, 'Vous n\'avez pas les autorisations requises', Response::HTTP_FORBIDDEN, ResponseStatus::ECHEC);
    }

    $validator = $request->only('prenom', 'nom', 'login', 'password', 'role', 'photo');
    $validator['password'] = bcrypt($request->password);

    $validator['role_id'] = $request->role == 'ADMIN' ? RoleEnum::ADMIN : RoleEnum::BOUTIQUIER;

     // Gestion de l'upload de photo
     if ($request->hasFile('photo')) {

        $path = $request->file('photo')->store('users', 'public');
        $validator['photo'] = $path;
    }

    $user = User::create($validator);
    $token = $user->createToken('api-token')->accessToken;

    return $this->sendResponse(['token' => $token, 'user' => $user], 'utilisateur cree avec succes', Response::HTTP_OK, ResponseStatus::SUCCESS);
}


    public function update(Request $request, $id)
{
    try {
        // Récupérer l'utilisateur à mettre à jour
        $user = User::findOrFail($id);

        // Validation des données
        $validate = $request->validate([
            'prenom' => 'sometimes|required|string|max:55|min:3',
            'nom' => 'sometimes|required|string|max:55|min:2',
            'login' => [
                'sometimes',
                'required',
                'email',
                'unique:users,login,' . $user->id,
            ],
            'password' => [
                'sometimes',
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&]/',
            ],
            'confirm_password' => 'sometimes|required_with:password|same:password',
            'role' => 'sometimes|required|in:ADMIN,BOUTIQUIER',
        ]);

    } catch (ValidationException $e) {
        return $this->errorResponse($e->errors(), 422);
    }

    // Mettre à jour les champs de l'utilisateur
    if (isset($validate['password'])) {
        $validate['password'] = bcrypt($validate['password']);
    }

    $user->update($validate);

    return $this->successResponse($user, 'Utilisateur mis à jour avec succès', 200);
}


public function destroy($id)
{
    try {
        $user = User::findOrFail($id);
        $user->delete();
        return $this->successResponse($user, 'Utilisateur supprimé avec succès', 200);
    } catch (ValidationException $e) {
        return $this->errorResponse($e->errors(), 422);
    }
}



public function show($id){
    try{
        $users = User::findOrFail($id);
        return $this->successResponse($users, 'Users retrieved successfully');
    }catch(ValidationException $e){
        return $this->errorResponse($e->errors(), 422);
    }

}


}
