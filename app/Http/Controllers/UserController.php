<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    use ApiResponser;
    //
    public function index ()
{
    try {
        $users = User::all();
        return $this->successResponse($users, 'liste des utilisateurs');
    } catch (ValidationException $e) {
        return $this->errorResponse($e->errors(), 422);
    }
}

    public function store(Request $request)
    {

        try {
            $validate = $request->validate([
                'prenom' => 'required|string|max:55|min:3',
                'nom' => 'required|string|max:55|min:2',
                'login' => 'required|email|unique:users',
                'password' => 'required|string|min:8|regex:/[A-Z]/|regex:/[a-z]/|regex:/[0-9]/|regex:/[@$!%*?&]/',
                'confirm_password' => 'required|same:password',
                'role' => 'required|in:ADMIN,BOUTIQUIER',
            ]);

        } catch (ValidationException $e) {
            return $this->errorResponse($e->errors(), 422);
        }


        $validate['login'] = strtolower($validate['login']);

        $validate['password'] = bcrypt($validate['password']);

        $user = User::where('login', $validate['login'])->first();
        if ($user) {
            return $this->errorResponse(null, 'User existe deja', 409);
        }

        $validate['password'] = bcrypt($validate['password']);
        $validate['role'] = strtoupper($validate['role']);

        $user = User::create($validate);
        return $this->successResponse($user, 'User cree avec succes',201);
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
