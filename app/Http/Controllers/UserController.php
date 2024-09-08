<?php

namespace App\Http\Controllers;
use App\Enums\ResponseStatus;
use App\Enums\RoleEnum;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\Contracts\IUserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;


class UserController extends Controller
{
    private $userService;
    public function __construct(IUserService  $userService)
    {
        $this->userService = $userService;
    }

    public function index (Request $request)
{
    try {
        $role = $request->query('role');
        $active = $request->query('active');
        $users = $this->userService->index($role, $active);

        return [
            'data' => $users,
            'message' => 'Users récupeérés avec_succès',
            'status' => ResponseStatus::SUCCESS,
            'code' => Response::HTTP_OK
        ] ;

    } catch (ValidationException $e) {
        return [
            'data' => null,
            'message' => $e->getMessage(),
            'status' => ResponseStatus::ECHEC,
            'code' => Response::HTTP_BAD_REQUEST
        ];
    }
}



public function store(RegisterRequest $request)
{
    if ($request->user()->cannot('admin')) {
        return $this->sendResponse(null, 'Vous n\'avez pas les autorisations requises', Response::HTTP_FORBIDDEN, ResponseStatus::ECHEC);
    }

    $validator = $request->only('prenom', 'nom', 'login', 'password', 'role', 'photo');
    $validator['role_id'] = $request->role == 'ADMIN' ? RoleEnum::ADMIN->value : RoleEnum::BOUTIQUIER->value;
    $user = $this->userService->create($validator);

    return [
        'data' => $user,
        'message' => 'Utilisateur enregistré avec_succès',
        'status' => ResponseStatus::SUCCESS,
        'code' => Response::HTTP_OK
    ];

}



    public function update(UpdateUserRequest $request, $id)
{
    try {
        $user = $this->userService->find($id);
        // Validation des données

    } catch (ValidationException $e) {
        return [
            'data' => null,
            'message' => $e->getMessage(),
            'status' => ResponseStatus::ECHEC,
            'code' => Response::HTTP_BAD_REQUEST
        ];
    }

    $validate = $request->only('prenom', 'nom', 'login', 'password', 'role', 'photo', 'active');
    // Mettre à jour les champs de l'utilisateur
    if (isset($validate['password'])) {
        $validate['password'] = bcrypt($validate['password']);
    }

    $user = $this->userService->update($validate, $id);

    return [
        'data' => $user,
        'message' => 'Utilisateur mis à jour avec_succès',
        'status' => ResponseStatus::SUCCESS,
        'code' => Response::HTTP_OK
    ];
}

public function destroy($id)
{
    try {
        $user = $this->userService->find($id);
        $user->delete();
        return [
            'data' => null,
            'message' => 'Utilisateur supprimé avec_succès',
            'status' => ResponseStatus::SUCCESS,
            'code' => Response::HTTP_OK
        ];
    } catch (ValidationException $e) {
        return [
            'data' => null,
            'message' => $e->getMessage(),
            'status' => ResponseStatus::ECHEC,
            'code' => Response::HTTP_BAD_REQUEST
        ];
    }
}

public function show($id){
    try{
        $users = $this->userService->find($id);
        return [
            'data' => $users,
            'message' => 'Utilisateur sélectionné avec_succès',
            'status' => ResponseStatus::SUCCESS,
            'code' => Response::HTTP_OK
        ];
    }catch(ValidationException $e){
        return [
            'data' => null,
            'message' => $e->getMessage(),
            'status' => ResponseStatus::ECHEC,
            'code' => Response::HTTP_BAD_REQUEST
        ];
    }
}

}
