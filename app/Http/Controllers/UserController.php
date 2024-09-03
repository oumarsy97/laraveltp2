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


/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="API Documentation",
 *         version="1.0.0",
 *         description="Documentation de l'API pour le projet"
 *     ),
 *     @OA\Server(
 *         url="http://127.0.0.1:3000/wane/v1",
 *         description="Serveur API de développement"
 *     ),
 *     @OA\Components(
 *         @OA\SecurityScheme(
 *             securityScheme="bearerAuth",
 *             type="http",
 *             scheme="bearer",
 *             bearerFormat="JWT",
 *             description="JWT authorization header using the Bearer scheme"
 *         )
 *     )
 * )
 */
class UserController extends Controller
{
    use ApiResponser;
    //

/**
 * @OA\Get(
 *     path="/users",
 *     summary="Get list of users",
 *     tags={"Users"},
 *     @OA\Response(
 *         response=200,
 *         description="Successful response",
 *     ),
 *     @OA\Response(
 *         response="default",
 *         description="unexpected error",
 *     ),
 )
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



/**
 * @OA\Post(
 *     path="/users/{id}",
 *     summary="Update an existing user",
 *     description="Creates a new user",
 *     operationId="createUser",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the user to be updated",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"prenom", "nom", "login", "password", "password_confirmation", "role"},
 *             @OA\Property(property="prenom", type="string", example="John"),
 *             @OA\Property(property="nom", type="string", example="Doe"),
 *             @OA\Property(property="login", type="string", format="email", example="johndoe@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="newpassword123"),
 *             @OA\Property(property="password_confirmation", type="string", format="password", example="newpassword123"),
 *             @OA\Property(property="role", type="string", enum={"ADMIN", "BOUTIQUIER"}, example="ADMIN")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="prenom", type="string", example="John"),
 *             @OA\Property(property="nom", type="string", example="Doe"),
 *             @OA\Property(property="login", type="string", example="johndoe@example.com"),
 *             @OA\Property(property="role", type="string", example="ADMIN"),
 *             @OA\Property(property="created_at", type="string", format="date-time", example="2023-09-01T12:34:56Z"),
 *             @OA\Property(property="updated_at", type="string", format="date-time", example="2023-09-01T12:34:56Z")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Unprocessable Entity: Validation errors",
 *         @OA\JsonContent(
 *             @OA\Property(property="errors", type="object", additionalProperties={"type":"array", "items":{"type":"string"}})
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Not Found: User not found"
 *     ),
 *     security={{"BearerAuth": {}}},
 * )
 */
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


/**
 * @OA\Put(
 *     path="/users/{id}",
 *     summary="Update an existing user",
 *     description="Updates the details of an existing user.",
 *     operationId="updateUser",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the user to be updated",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"prenom", "nom", "login", "password", "password_confirmation", "role"},
 *             @OA\Property(property="prenom", type="string", example="John"),
 *             @OA\Property(property="nom", type="string", example="Doe"),
 *             @OA\Property(property="login", type="string", format="email", example="johndoe@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="newpassword123"),
 *             @OA\Property(property="password_confirmation", type="string", format="password", example="newpassword123"),
 *             @OA\Property(property="role", type="string", enum={"ADMIN", "BOUTIQUIER"}, example="ADMIN")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="prenom", type="string", example="John"),
 *             @OA\Property(property="nom", type="string", example="Doe"),
 *             @OA\Property(property="login", type="string", example="johndoe@example.com"),
 *             @OA\Property(property="role", type="string", example="ADMIN"),
 *             @OA\Property(property="created_at", type="string", format="date-time", example="2023-09-01T12:34:56Z"),
 *             @OA\Property(property="updated_at", type="string", format="date-time", example="2023-09-01T12:34:56Z")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Unprocessable Entity: Validation errors",
 *         @OA\JsonContent(
 *             @OA\Property(property="errors", type="object", additionalProperties={"type":"array", "items":{"type":"string"}})
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Not Found: User not found"
 *     ),
 *     security={{"BearerAuth": {}}},
 * )
 */

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

/**
 * @OA\Delete(
 *     path="/users/{id}",
 *     summary="Delete a user",
 *     description="Deletes a user by ID.",
 *     operationId="deleteUser",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the user to be deleted",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="prenom", type="string", example="John"),
 *             @OA\Property(property="nom", type="string", example="Doe"),
 *             @OA\Property(property="login", type="string", example="johndoe@example.com"),
 *             @OA\Property(property="role", type="string", example="ADMIN"),
 *             @OA\Property(property="created_at", type="string", format="date-time", example="2023-09-01T12:34:56Z"),
 *             @OA\Property(property="updated_at", type="string", format="date-time", example="2023-09-01T12:34:56Z")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Not Found: User not found"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Unprocessable Entity: Validation errors",
 *         @OA\JsonContent(
 *             @OA\Property(property="errors", type="object", additionalProperties={"type":"array", "items":{"type":"string"}})
 *         )
 *     ),
 *     security={{"BearerAuth": {}}},
 * )
 */

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
