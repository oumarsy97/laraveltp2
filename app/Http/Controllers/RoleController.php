<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    //
    public function index()
    {
        $roles = Role::all();
        return response()->json($roles, 200);
    }


    public function store(Request $request)
    {
        $role = Role::create($request->all());
        return response()->json($role, 200);
    }


    public function update(Request $request, Role $role)
    {
        $role->update($request->all());
        return response()->json($role, 200);
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return response()->json(null, 204);
    }

    public function show(int $id)
    {
        $role = Role::find($id);
        return response()->json($role, 200);
    }


    /**
     * Retourne le libellé du rôle pour un ID donné.
     *
     * @param int $id
     * @return string|null
     */
    public static function getRole(int $id): ?string
    {
        $role = Role::find($id);

        // Retourner le libellé si trouvé, sinon null
        return $role ? $role->libelle : null;
    }
}
